<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RecetaMedica;
use App\Models\Venta;

class FarmaceuticoDashboardController extends Controller
{
    public function index()
    {
        $recetasPendientes = RecetaMedica::where('estado_validacion', false)->count();
        $recetasHoy = RecetaMedica::whereDate('created_at', today())->count();
        $ventasAnulables = Venta::whereDate('fecha_hora', today())
            ->where('estado', 'Pagada')
            ->count();

        return view('dashboards.farmaceutico.index', compact(
            'recetasPendientes',
            'recetasHoy',
            'ventasAnulables'
        ));
    }
}