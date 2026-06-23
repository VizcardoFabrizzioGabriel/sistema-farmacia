<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GeminiService;

class GeminiController extends Controller
{
    private GeminiService $gemini;

    public function __construct()
    {
        $this->gemini = new GeminiService();
    }

    public function consultar(Request $request)
    {
        $request->validate([
            'pregunta' => 'required|string|max:1000',
        ]);

        $resultado = $this->gemini->consultar($request->pregunta);

        return response()->json($resultado);
    }

    public function interacciones(Request $request)
    {
        $request->validate([
            'medicamento1' => 'required|string',
            'medicamento2' => 'required|string',
        ]);

        $resultado = $this->gemini->consultarInteracciones(
            $request->medicamento1,
            $request->medicamento2
        );

        return response()->json($resultado);
    }

    public function infoProducto(Request $request)
    {
        $request->validate([
            'producto' => 'required|string',
        ]);

        $resultado = $this->gemini->informacionProducto($request->producto);

        return response()->json($resultado);
    }
}