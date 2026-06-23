<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cliente' => 'nullable|exists:clientes,id_cliente',
            'id_receta' => 'nullable|exists:recetas_medicas,id_receta',
            'metodo_pago' => 'required|string|in:Efectivo,Tarjeta,Stripe',
            'tipo_comprobante' => 'required|string|in:Boleta,Factura',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'metodo_pago.in' => 'Método de pago no válido.',
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio.',
            'productos.required' => 'Debe agregar al menos un producto.',
            'productos.*.id_producto.required' => 'Producto inválido.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}