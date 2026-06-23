<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\Proveedor;
use App\Services\MapaService;
use App\Services\InventarioService;

class AlmacenDashboardController extends Controller
{
    public function index()
    {
        $totalProductos = Producto::count();
        $lotesPorVencer = Lote::where('estado', 'PorVencer')->count();
        $lotesCuarentena = Lote::where('estado', 'Cuarentena')->count();
        $totalProveedores = Proveedor::count();

        $inventarioService = new InventarioService();
        $sugerenciasCompra = $inventarioService->generarSugerenciaCompra();

        return view('dashboards.almacen.index', compact(
            'totalProductos',
            'lotesPorVencer',
            'lotesCuarentena',
            'totalProveedores',
            'sugerenciasCompra'
        ));
    }
}