<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalAdministrativo extends Empleado
{
    use HasFactory;

    protected $table = 'empleados';

    /**
     * Boot del modelo para filtrar solo personal administrativo
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('administrativo', function ($query) {
            $query->whereHas('usuario', function ($q) {
                $q->where('id_rol', 1); // Rol Administrativo
            });
        });
    }

    // Métodos específicos del Personal Administrativo
    public function generarReporteVentas(string $fechaInicio, string $fechaFin): array
    {
        $ventas = Venta::whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->where('estado', 'Pagada')
            ->with(['detalles.producto', 'empleado.usuario', 'cliente.usuario'])
            ->get();

        $totalVentas = $ventas->count();
        $montoTotal = $ventas->sum('total');
        $promedioVenta = $ventas->avg('total');

        $ventasPorEmpleado = $ventas->groupBy('id_empleado')->map(function ($grupo) {
            return [
                'empleado' => $grupo->first()->empleado->usuario->nombres . ' ' . $grupo->first()->empleado->usuario->apellidos,
                'cantidad' => $grupo->count(),
                'monto' => $grupo->sum('total'),
            ];
        })->values();

        return [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin,
            ],
            'resumen' => [
                'total_ventas' => $totalVentas,
                'monto_total' => round($montoTotal, 2),
                'promedio_venta' => round($promedioVenta, 2),
            ],
            'ventas_por_empleado' => $ventasPorEmpleado,
            'ventas_detalle' => $ventas->map(function ($v) {
                return [
                    'id' => $v->id_venta,
                    'fecha' => $v->fecha_hora->format('d/m/Y H:i'),
                    'cliente' => $v->cliente ? $v->cliente->usuario->nombres . ' ' . $v->cliente->usuario->apellidos : 'Cliente General',
                    'empleado' => $v->empleado->usuario->nombres . ' ' . $v->empleado->usuario->apellidos,
                    'total' => $v->total,
                    'metodo_pago' => $v->metodo_pago,
                ];
            }),
        ];
    }

    public function gestionarUsuarios(): array
    {
        return [
            'total_usuarios' => User::count(),
            'usuarios_activos' => User::where('estado', true)->count(),
            'usuarios_suspendidos' => User::where('estado', false)->count(),
            'por_rol' => \App\Models\Rol::withCount('usuarios')->get()->map(function ($rol) {
                return [
                    'rol' => $rol->nombre,
                    'cantidad' => $rol->usuarios_count,
                ];
            }),
        ];
    }

    public function aplicarDescuentoEspecial(int $idVenta, float $porcentajeDescuento): Venta
    {
        $venta = Venta::findOrFail($idVenta);

        if ($venta->estado !== 'Pendiente') {
            throw new \Exception('Solo se puede aplicar descuento a ventas pendientes.');
        }

        $nuevoSubtotal = $venta->subtotal * (1 - ($porcentajeDescuento / 100));
        $venta->subtotal = round($nuevoSubtotal, 2);
        $venta->impuestos = round($venta->subtotal * 0.18, 2);
        $venta->total = round($venta->subtotal + $venta->impuestos, 2);
        $venta->save();

        // Actualizar detalles con descuento proporcional
        foreach ($venta->detalles as $detalle) {
            $detalle->descuento = round($detalle->subtotal * ($porcentajeDescuento / 100), 2);
            $detalle->subtotal = round($detalle->subtotal - $detalle->descuento, 2);
            $detalle->save();
        }

        return $venta;
    }

    public function procesarReembolso(int $idVenta): array
    {
        $venta = Venta::findOrFail($idVenta);

        if ($venta->estado !== 'Pagada') {
            return [
                'success' => false,
                'message' => 'Solo se pueden reembolsar ventas pagadas.',
            ];
        }

        // Si fue pago con Stripe, procesar reembolso
        if ($venta->stripe_payment_id) {
            $pasarela = new \App\Services\PasarelaPagoService();
            $resultado = $pasarela->procesarReembolso($venta);

            if (!$resultado['success']) {
                return $resultado;
            }
        }

        // Anular la venta
        $venta->anular();

        return [
            'success' => true,
            'message' => 'Reembolso procesado correctamente.',
            'venta_id' => $venta->id_venta,
            'monto_reembolsado' => $venta->total,
        ];
    }

    public function corteDeCaja(string $fecha = null): array
    {
        $fecha = $fecha ?? now()->format('Y-m-d');

        $ventasDia = Venta::whereDate('fecha_hora', $fecha)
            ->where('estado', 'Pagada')
            ->get();

        $porMetodo = $ventasDia->groupBy('metodo_pago')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->count(),
                'monto' => round($grupo->sum('total'), 2),
            ];
        });

        return [
            'fecha' => $fecha,
            'total_ventas' => $ventasDia->count(),
            'monto_total' => round($ventasDia->sum('total'), 2),
            'monto_efectivo' => round($ventasDia->where('metodo_pago', 'Efectivo')->sum('total'), 2),
            'monto_tarjeta' => round($ventasDia->where('metodo_pago', 'Tarjeta')->sum('total'), 2),
            'monto_stripe' => round($ventasDia->where('metodo_pago', 'Stripe')->sum('total'), 2),
            'detalle_por_metodo' => $porMetodo,
            'ventas' => $ventasDia->map(function ($v) {
                return [
                    'id' => $v->id_venta,
                    'hora' => $v->fecha_hora->format('H:i'),
                    'total' => $v->total,
                    'metodo' => $v->metodo_pago,
                    'empleado' => $v->empleado->usuario->nombres,
                ];
            }),
        ];
    }
}