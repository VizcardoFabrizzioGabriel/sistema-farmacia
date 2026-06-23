<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->paginate(15);
        return view('dashboards.admin.productos', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'codigo_barras' => 'required|string|unique:productos,codigo_barras',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'es_controlado' => 'boolean',
            'requiere_receta' => 'boolean',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        Producto::create([
            'id_categoria' => $request->id_categoria,
            'codigo_barras' => $request->codigo_barras,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio_venta' => $request->precio_venta,
            'es_controlado' => $request->boolean('es_controlado'),
            'requiere_receta' => $request->boolean('requiere_receta'),
            'stock_actual' => 0,
            'stock_minimo' => $request->stock_minimo,
        ]);

        return redirect()->back()->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'nombre' => 'required|string|max:150',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $producto->update($request->only([
            'id_categoria', 'nombre', 'descripcion', 'precio_venta',
            'es_controlado', 'requiere_receta', 'stock_minimo'
        ]));

        return redirect()->back()->with('success', 'Producto actualizado.');
    }

    public function destroy(int $id)
    {
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado.');
    }

    // Catálogo para clientes
    public function catalogo()
    {
        $productos = Producto::where('stock_actual', '>', 0)
            ->with('categoria')
            ->paginate(12);

        return view('dashboards.cliente.catalogo', compact('productos'));
    }

    // API: Productos disponibles
    public function apiDisponibles()
    {
        $productos = Producto::where('stock_actual', '>', 0)
            ->select('id_producto', 'nombre', 'precio_venta', 'stock_actual', 'es_controlado', 'requiere_receta')
            ->get();

        return response()->json($productos);
    }
}