<?php

namespace App\Services;

use App\Models\RecetaMedica;
use App\Models\Producto;

class RecetaService
{
    // Validar si una venta requiere receta
    public function requiereReceta(array $productosIds): bool
    {
        return Producto::whereIn('id_producto', $productosIds)
            ->where(function ($query) {
                $query->where('es_controlado', true)
                      ->orWhere('requiere_receta', true);
            })
            ->exists();
    }

    // Validar receta médica
    public function validarReceta(string $codigoReceta, string $dniMedico): array
    {
        $receta = RecetaMedica::where('codigo_receta', $codigoReceta)
            ->where('dni_medico', $dniMedico)
            ->first();

        if (!$receta) {
            return [
                'success' => false,
                'message' => 'Receta no encontrada.',
            ];
        }

        if (!$receta->verificarVigencia()) {
            return [
                'success' => false,
                'message' => 'La receta ha vencido o no está validada.',
            ];
        }

        return [
            'success' => true,
            'receta' => $receta,
            'message' => 'Receta válida.',
        ];
    }

    // Crear nueva receta
    public function crearReceta(array $datos): RecetaMedica
    {
        return RecetaMedica::create([
            'codigo_receta' => $datos['codigo_receta'],
            'dni_medico' => $datos['dni_medico'],
            'nombre_medico' => $datos['nombre_medico'],
            'fecha_emision' => $datos['fecha_emision'] ?? now(),
            'url_imagen' => $datos['url_imagen'] ?? null,
            'estado_validacion' => false, // Requiere aprobación del farmacéutico
        ]);
    }

    // Aprobar receta (solo farmacéutico)
    public function aprobarReceta(int $idReceta): array
    {
        $receta = RecetaMedica::findOrFail($idReceta);
        $receta->estado_validacion = true;
        $receta->save();

        return [
            'success' => true,
            'message' => 'Receta aprobada por el farmacéutico.',
            'receta' => $receta,
        ];
    }
}