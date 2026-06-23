<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;

class ClienteDashboardController extends Controller
{
    public function index()
    {
        $cliente = auth()->user()->cliente;
        
        $totalCompras = Venta::where('id_cliente', $cliente->id_cliente)
            ->where('estado', 'Pagada')
            ->count();
        
        $totalGastado = Venta::where('id_cliente', $cliente->id_cliente)
            ->where('estado', 'Pagada')
            ->sum('total');

        $puntosFidelidad = $cliente->puntos_fidelidad;

        $productosDestacados = Producto::where('stock_actual', '>', 0)
            ->orderBy('stock_actual', 'desc')
            ->take(6)
            ->get();

        return view('dashboards.cliente.index', compact(
            'totalCompras',
            'totalGastado',
            'puntosFidelidad',
            'productosDestacados'
        ));
    }
}