<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Lote;

class FEFOService
{
    // Descuenta stock aplicando FEFO (First Expired, First Out)
    public function descontarStockFEFO(int $idProducto, int $cantidad): array
    {
        $producto = Producto::findOrFail($idProducto);
        
        if ($producto->stock_actual < $cantidad) {
            return [
                'success' => false,
                'message' => 'Stock insuficiente. Stock actual: ' . $producto->stock_actual
            ];
        }

        $lotes = Lote::where('id_producto', $idProducto)
            ->whereIn('estado', ['Activo', 'PorVencer'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();

        $cantidadRestante = $cantidad;
        $lotesDescontados = [];

        foreach ($lotes as $lote) {
            if ($cantidadRestante <= 0) break;

            $cantidadADescontar = min($cantidadRestante, $lote->cantidad_actual);
            
            $lote->cantidad_actual -= $cantidadADescontar;
            $cantidadRestante -= $cantidadADescontar;

            if ($lote->cantidad_actual == 0) {
                $lote->estado = 'Baja';
            }

            $lote->save();
            
            $lotesDescontados[] = [
                'lote' => $lote->numero_lote,
                'cantidad_descontada' => $cantidadADescontar,
                'fecha_vencimiento' => $lote->fecha_vencimiento,
            ];
        }

        $producto->actualizarStock();

        return [
            'success' => true,
            'lotes_descontados' => $lotesDescontados,
            'cantidad_total' => $cantidad,
            'stock_restante' => $producto->stock_actual,
        ];
    }

    // Obtener lotes ordenados por FEFO para mostrar en UI
    public function obtenerLotesFEFO(int $idProducto): array
    {
        $lotes = Lote::where('id_producto', $idProducto)
            ->whereIn('estado', ['Activo', 'PorVencer'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->get()
            ->map(function ($lote) {
                return [
                    'numero_lote' => $lote->numero_lote,
                    'cantidad_actual' => $lote->cantidad_actual,
                    'fecha_vencimiento' => $lote->fecha_vencimiento->format('d/m/Y'),
                    'estado' => $lote->estado,
                    'dias_para_vencer' => now()->diffInDays($lote->fecha_vencimiento, false),
                ];
            });

        return $lotes->toArray();
    }
}