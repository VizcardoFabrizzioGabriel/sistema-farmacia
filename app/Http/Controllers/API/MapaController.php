<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MapaService;

class MapaController extends Controller
{
    private MapaService $mapaService;

    public function __construct()
    {
        $this->mapaService = new MapaService();
    }

    // Vista del mapa para el almacén
    public function index()
    {
        $farmacia = $this->mapaService->getCoordenadasFarmacia();
        $proveedores = $this->mapaService->getProveedoresPorDistancia();

        return view('dashboards.almacen.mapa', compact('farmacia', 'proveedores'));
    }

    // API: Listar proveedores con coordenadas
    public function listarProveedores()
    {
        $proveedores = $this->mapaService->getProveedoresPorDistancia();

        return response()->json([
            'success' => true,
            'farmacia' => $this->mapaService->getCoordenadasFarmacia(),
            'proveedores' => $proveedores,
        ]);
    }
}