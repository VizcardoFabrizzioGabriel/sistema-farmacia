<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function consultar(string $pregunta): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$this->apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $pregunta]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $respuesta = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No se obtuvo respuesta.';

                return [
                    'success' => true,
                    'respuesta' => $respuesta,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error en la API de Gemini.',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    // Consultar interacciones medicamentosas
    public function consultarInteracciones(string $medicamento1, string $medicamento2): array
    {
        $pregunta = "Como farmacéutico experto, indica si existe alguna interacción medicamentosa entre {$medicamento1} y {$medicamento2}. Sé conciso y claro. Si hay interacción, indica el nivel de riesgo (bajo, medio, alto) y recomendaciones.";
        
        return $this->consultar($pregunta);
    }

    // Información general de producto
    public function informacionProducto(string $nombreProducto): array
    {
        $pregunta = "Proporciona información útil sobre el medicamento {$nombreProducto}: para qué sirve, dosis recomendada, contraindicaciones y efectos secundarios comunes. Sé breve y profesional.";
        
        return $this->consultar($pregunta);
    }
}