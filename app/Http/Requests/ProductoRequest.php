<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productoId = $this->route('id');

        return [
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'codigo_barras' => 'required|string|max:50|unique:productos,codigo_barras,' . $productoId . ',id_producto',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'es_controlado' => 'boolean',
            'requiere_receta' => 'boolean',
            'stock_minimo' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'id_categoria.required' => 'La categoría es obligatoria.',
            'codigo_barras.required' => 'El código de barras es obligatorio.',
            'codigo_barras.unique' => 'Este código de barras ya existe.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.min' => 'El precio debe ser mayor o igual a 0.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
        ];
    }
}