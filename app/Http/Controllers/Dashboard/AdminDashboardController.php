<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Categoria;
use App\Models\Empleado;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalUsuarios = User::count();
        $totalProductos = Producto::count();
        $totalVentasHoy = Venta::whereDate('fecha_hora', Carbon::today())->count();
        $montoVentasHoy = Venta::whereDate('fecha_hora', Carbon::today())
            ->where('estado', 'Pagada')
            ->sum('total');

        $stockBajo = Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->count();

        // Ventas de los últimos 7 días
        $ventasSemana = Venta::where('estado', 'Pagada')
            ->whereBetween('fecha_hora', [Carbon::now()->subDays(7), Carbon::now()])
            ->selectRaw('DATE(fecha_hora) as fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('dashboards.admin.index', compact(
            'totalUsuarios',
            'totalProductos',
            'totalVentasHoy',
            'montoVentasHoy',
            'stockBajo',
            'ventasSemana'
        ));
    }
}