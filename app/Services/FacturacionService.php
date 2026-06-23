<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\Comprobante;

class FacturacionService
{
    public function generarComprobante(Venta $venta, string $tipo = 'Boleta'): Comprobante
    {
        $comprobante = new Comprobante();
        $comprobante->id_venta = $venta->id_venta;
        $comprobante->tipo = $tipo;
        $comprobante->numero_serie = Comprobante::generarNumeroSerie($tipo);
        $comprobante->save();

        // Generar XML
        $xml = $comprobante->generarXML();

        // Emitir PDF (simulado - en producción usaría DomPDF o similar)
        $comprobante->emitirPDF();

        return $comprobante;
    }

    public function generarTicket(Venta $venta): array
    {
        $detalles = $venta->detalles->map(function ($detalle) {
            return [
                'producto' => $detalle->producto->nombre,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'subtotal' => $detalle->subtotal,
            ];
        });

        return [
            'numero_serie' => $venta->comprobante ? $venta->comprobante->numero_serie : 'PENDIENTE',
            'fecha' => $venta->fecha_hora->format('d/m/Y H:i'),
            'cliente' => $venta->cliente ? $venta->cliente->usuario->nombres . ' ' . $venta->cliente->usuario->apellidos : 'Cliente General',
            'empleado' => $venta->empleado->usuario->nombres . ' ' . $venta->empleado->usuario->apellidos,
            'detalles' => $detalles,
            'subtotal' => $venta->subtotal,
            'impuestos' => $venta->impuestos,
            'total' => $venta->total,
            'metodo_pago' => $venta->metodo_pago,
        ];
    }
}