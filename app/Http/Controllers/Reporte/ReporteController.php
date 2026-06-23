<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Empleado;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Ventas por período
        $ventasPeriodo = Venta::whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->where('estado', 'Pagada')
            ->get();

        $totalVentas = $ventasPeriodo->count();
        $montoTotal = $ventasPeriodo->sum('total');
        $promedioVenta = $ventasPeriodo->avg('total');

        // Ventas por empleado
        $ventasPorEmpleado = Venta::whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->where('estado', 'Pagada')
            ->selectRaw('id_empleado, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('id_empleado')
            ->with('empleado.usuario')
            ->get();

        // Productos más vendidos
        $productosTop = \App\Models\DetalleVenta::whereHas('venta', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                  ->where('estado', 'Pagada');
            })
            ->selectRaw('id_producto, SUM(cantidad) as total_vendido, SUM(subtotal) as monto_total')
            ->groupBy('id_producto')
            ->with('producto')
            ->orderBy('total_vendido', 'desc')
            ->take(10)
            ->get();

        // Ventas por día (para gráfico)
        $ventasPorDia = Venta::whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->where('estado', 'Pagada')
            ->selectRaw('DATE(fecha_hora) as fecha, COUNT(*) as cantidad, SUM(total) as monto')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('dashboards.admin.reportes', compact(
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'montoTotal',
            'promedioVenta',
            'ventasPorEmpleado',
            'productosTop',
            'ventasPorDia'
        ));
    }
}