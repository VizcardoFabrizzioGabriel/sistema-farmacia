<?php

namespace App\Http\Controllers\Venta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Services\CarritoService;

class CarritoController extends Controller
{
    private CarritoService $carrito;

    public function __construct()
    {
        $this->carrito = new CarritoService();
    }

    public function add(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|integer|exists:productos,id_producto',
            'cantidad' => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($request->id_producto);

        // Validar stock
        if ($producto->stock_actual < $request->cantidad) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $producto->stock_actual,
            ]);
        }

        $this->carrito->agregarProducto(
            $producto->id_producto,
            $request->cantidad,
            $producto->precio_venta,
            $producto->nombre
        );

        return response()->json([
            'success' => true,
            'carrito' => $this->carrito->calcularTotales(),
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|integer',
        ]);

        $this->carrito->quitarProducto($request->id_producto);

        return response()->json([
            'success' => true,
            'carrito' => $this->carrito->calcularTotales(),
        ]);
    }

    public function get()
    {
        return response()->json([
            'success' => true,
            'carrito' => $this->carrito->calcularTotales(),
        ]);
    }

    public function clear()
    {
        $this->carrito->vaciarCarrito();

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado.',
        ]);
    }
}