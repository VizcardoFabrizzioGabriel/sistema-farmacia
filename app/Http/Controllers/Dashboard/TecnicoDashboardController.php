<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;

class TecnicoDashboardController extends Controller
{
    public function index()
    {
        $productosDisponibles = Producto::where('stock_actual', '>', 0)->count();
        $misVentasHoy = Venta::where('id_empleado', auth()->user()->empleado->id_empleado)
            ->whereDate('fecha_hora', Carbon::today())
            ->count();

        return view('dashboards.tecnico.index', compact(
            'productosDisponibles',
            'misVentasHoy'
        ));
    }
}