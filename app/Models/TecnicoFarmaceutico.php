<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TecnicoFarmaceutico extends Empleado
{
    use HasFactory;

    protected $table = 'empleados';

    /**
     * Boot del modelo para filtrar solo tecnicos farmaceuticos
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('tecnico_farmaceutico', function ($query) {
            $query->whereHas('usuario', function ($q) {
                $q->where('id_rol', 3); // Rol TecnicoFarmaceutico
            });
        });
    }

    // Métodos específicos del Técnico Farmacéutico
    public function consultarStock(int $idProducto = null): array
    {
        if ($idProducto) {
            $producto = Producto::with('lotes')->findOrFail($idProducto);
            return [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'stock_actual' => $producto->stock_actual,
                'stock_minimo' => $producto->stock_minimo,
                'estado_stock' => $producto->stock_actual <= $producto->stock_minimo ? 'Bajo' : 'Normal',
                'lotes' => $producto->lotes->map(function ($lote) {
                    return [
                        'numero_lote' => $lote->numero_lote,
                        'cantidad_actual' => $lote->cantidad_actual,
                        'fecha_vencimiento' => $lote->fecha_vencimiento->format('d/m/Y'),
                        'estado' => $lote->estado,
                    ];
                }),
            ];
        }

        // Retornar todos los productos con stock bajo
        $productosBajos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();

        return [
            'productos_bajo_stock' => $productosBajos->map(function ($p) {
                return [
                    'id' => $p->id_producto,
                    'nombre' => $p->nombre,
                    'stock_actual' => $p->stock_actual,
                    'stock_minimo' => $p->stock_minimo,
                ];
            }),
            'total_productos_bajos' => $productosBajos->count(),
        ];
    }

    public function registrarVentaBasica(array $productos, string $metodoPago = 'Efectivo'): Venta
    {
        // Verificar que no haya productos controlados
        $idsProductos = array_column($productos, 'id_producto');
        $hayControlados = Producto::whereIn('id_producto', $idsProductos)
            ->where(function ($query) {
                $query->where('es_controlado', true)
                      ->orWhere('requiere_receta', true);
            })
            ->exists();

        if ($hayControlados) {
            throw new \Exception('No puede registrar venta de medicamentos controlados. Requiere validación del farmacéutico.');
        }

        $carrito = new \App\Services\CarritoService();
        $fefo = new \App\Services\FEFOService();

        DB::beginTransaction();

        try {
            $subtotal = 0;

            foreach ($productos as $item) {
                $producto = Producto::findOrFail($item['id_producto']);
                $subtotal += $producto->precio_venta * $item['cantidad'];
            }

            $impuestos = $subtotal * 0.18;
            $total = $subtotal + $impuestos;

            $venta = Venta::create([
                'id_cliente' => null,
                'id_empleado' => $this->id_empleado,
                'id_receta' => null,
                'fecha_hora' => now(),
                'subtotal' => round($subtotal, 2),
                'impuestos' => round($impuestos, 2),
                'total' => round($total, 2),
                'metodo_pago' => $metodoPago,
                'estado' => 'Pagada',
            ]);

            foreach ($productos as $item) {
                $producto = Producto::findOrFail($item['id_producto']);

                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'descuento' => 0,
                    'subtotal' => round($producto->precio_venta * $item['cantidad'], 2),
                ]);

                $fefo->descontarStockFEFO($item['id_producto'], $item['cantidad']);
            }

            DB::commit();

            // Generar comprobante
            $facturacion = new \App\Services\FacturacionService();
            $facturacion->generarComprobante($venta, 'Boleta');

            // Notificar
            $telegram = new \App\Services\TelegramBotService();
            $telegram->notificarNuevaVenta($venta->total, $this->usuario->nombres . ' ' . $this->usuario->apellidos);

            return $venta;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function notificarFaltaStock(int $idProducto): array
    {
        $producto = Producto::findOrFail($idProducto);

        if ($producto->stock_actual > $producto->stock_minimo) {
            return [
                'success' => false,
                'message' => 'El producto aún tiene stock suficiente.',
            ];
        }

        $telegram = new \App\Services\TelegramBotService();
        $telegram->notificarStockBajo($producto->nombre, $producto->stock_actual, $producto->stock_minimo);

        return [
            'success' => true,
            'message' => 'Notificación enviada al encargado de almacén.',
        ];
    }

    public function consultarHistorialRecetasCliente(int $idCliente): array
    {
        $cliente = Cliente::findOrFail($idCliente);

        $ventasConReceta = Venta::where('id_cliente', $idCliente)
            ->whereNotNull('id_receta')
            ->with(['recetaMedica', 'detalles.producto'])
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return [
            'cliente' => $cliente->usuario->nombres . ' ' . $cliente->usuario->apellidos,
            'total_recetas' => $ventasConReceta->count(),
            'recetas' => $ventasConReceta->map(function ($v) {
                return [
                    'id_venta' => $v->id_venta,
                    'fecha' => $v->fecha_hora->format('d/m/Y H:i'),
                    'codigo_receta' => $v->recetaMedica->codigo_receta,
                    'medico' => $v->recetaMedica->nombre_medico,
                    'productos' => $v->detalles->map(function ($d) {
                        return $d->producto->nombre;
                    }),
                ];
            }),
        ];
    }
}