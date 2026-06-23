<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecetaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_receta' => 'required|string|max:50|unique:recetas_medicas,codigo_receta',
            'dni_medico' => 'required|string|max:15',
            'nombre_medico' => 'required|string|max:150',
            'fecha_emision' => 'required|date|before_or_equal:today',
            'url_imagen' => 'nullable|string|max:255',
            'estado_validacion' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_receta.required' => 'El código de receta es obligatorio.',
            'codigo_receta.unique' => 'Este código de receta ya existe.',
            'dni_medico.required' => 'El DNI del médico es obligatorio.',
            'nombre_medico.required' => 'El nombre del médico es obligatorio.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.before_or_equal' => 'La fecha no puede ser futura.',
        ];
    }
}