<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farmaceutico extends Empleado
{
    use HasFactory;

    protected $table = 'empleados';

    /**
     * Boot del modelo para filtrar solo farmaceuticos
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('farmaceutico', function ($query) {
            $query->whereHas('usuario', function ($q) {
                $q->where('id_rol', 2); // Rol Farmaceutico
            });
        });
    }

    // Métodos específicos del Farmaceutico
    public function validarRecetaMedica(int $idReceta): array
    {
        $service = new \App\Services\RecetaService();
        return $service->aprobarReceta($idReceta);
    }

    public function autorizarVentaControlada(int $idVenta): Venta
    {
        $venta = Venta::findOrFail($idVenta);

        if ($venta->estado !== 'Validando') {
            throw new \Exception('La venta no está en estado de validación.');
        }

        $venta->estado = 'Pagada';
        $venta->save();

        // Generar comprobante
        $facturacion = new \App\Services\FacturacionService();
        $facturacion->generarComprobante($venta, 'Boleta');

        return $venta;
    }

    public function anularVenta(int $idVenta): array
    {
        $venta = Venta::findOrFail($idVenta);

        if ($venta->anular()) {
            return [
                'success' => true,
                'message' => 'Venta anulada correctamente. Stock devuelto al inventario.'
            ];
        }

        return [
            'success' => false,
            'message' => 'No se puede anular esta venta. Solo se permiten anulaciones el mismo día del turno.'
        ];
    }

    public function consultarInteraccionesMedicamentosas(string $medicamento1, string $medicamento2): array
    {
        $service = new \App\Services\GeminiService();
        return $service->consultarInteracciones($medicamento1, $medicamento2);
    }
}