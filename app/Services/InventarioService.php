<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Lote;
use App\Services\TelegramBotService;

class InventarioService
{
    private TelegramBotService $telegram;

    public function __construct()
    {
        $this->telegram = new TelegramBotService();
    }

    // Verificar y alertar stock bajo
    public function verificarStockBajo(): array
    {
        $productosBajos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
        $alertas = [];

        foreach ($productosBajos as $producto) {
            $alertas[] = [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'stock_actual' => $producto->stock_actual,
                'stock_minimo' => $producto->stock_minimo,
            ];

            $this->telegram->notificarStockBajo(
                $producto->nombre,
                $producto->stock_actual,
                $producto->stock_minimo
            );
        }

        return $alertas;
    }

    // Verificar caducidad de lotes y actualizar estados
    public function verificarCaducidadLotes(): array
    {
        $lotes = Lote::whereIn('estado', ['Activo', 'PorVencer'])->get();
        $alertas = [];

        foreach ($lotes as $lote) {
            $estadoAnterior = $lote->estado;
            $nuevoEstado = $lote->evaluarEstadoCaducidad();

            if ($estadoAnterior !== $nuevoEstado) {
                $alertas[] = [
                    'lote' => $lote->numero_lote,
                    'producto' => $lote->producto->nombre,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $nuevoEstado,
                ];

                if (in_array($nuevoEstado, ['PorVencer', 'Cuarentena'])) {
                    $this->telegram->notificarCaducidad(
                        $lote->numero_lote,
                        $lote->producto->nombre,
                        $lote->fecha_vencimiento->format('d/m/Y'),
                        $nuevoEstado
                    );
                }
            }
        }

        return $alertas;
    }

    // Generar reporte de sugerencia de compra
    public function generarSugerenciaCompra(): array
    {
        $productos = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orWhereHas('lotes', function ($query) {
                $query->where('estado', 'Cuarentena');
            })
            ->with(['lotes' => function ($query) {
                $query->whereIn('estado', ['Activo', 'PorVencer']);
            }])
            ->get();

        $sugerencias = [];

        foreach ($productos as $producto) {
            $stockDisponible = $producto->lotes->sum('cantidad_actual');
            $cantidadSugerida = max($producto->stock_minimo * 2 - $stockDisponible, 0);

            if ($cantidadSugerida > 0) {
                $sugerencias[] = [
                    'id_producto' => $producto->id_producto,
                    'nombre' => $producto->nombre,
                    'stock_actual' => $stockDisponible,
                    'stock_minimo' => $producto->stock_minimo,
                    'cantidad_sugerida' => $cantidadSugerida,
                    'proveedor_principal' => $producto->lotes->first()?->proveedor->razon_social ?? 'Sin proveedor',
                ];
            }
        }

        return $sugerencias;
    }

    // Auditar inventario físico vs lógico
    public function auditarInventario(): array
    {
        $productos = Producto::with('lotes')->get();
        $discrepancias = [];

        foreach ($productos as $producto) {
            $stockPorLotes = $producto->lotes
                ->whereIn('estado', ['Activo', 'PorVencer'])
                ->sum('cantidad_actual');

            if ($producto->stock_actual != $stockPorLotes) {
                $discrepancias[] = [
                    'id_producto' => $producto->id_producto,
                    'nombre' => $producto->nombre,
                    'stock_logico' => $producto->stock_actual,
                    'stock_fisico_lotes' => $stockPorLotes,
                    'diferencia' => $stockPorLotes - $producto->stock_actual,
                ];

                // Corregir automáticamente
                $producto->stock_actual = $stockPorLotes;
                $producto->save();
            }
        }

        return $discrepancias;
    }
}