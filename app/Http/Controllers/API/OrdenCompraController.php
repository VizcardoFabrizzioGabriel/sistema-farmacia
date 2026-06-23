<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrdenCompra;
use App\Models\DetalleOrden;
use App\Models\Proveedor;
use App\Models\Producto;

class OrdenCompraController extends Controller
{
    public function index()
    {
        $ordenes = OrdenCompra::with(['proveedor', 'empleado.usuario', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('dashboards.almacen.ordenes_compra', compact('ordenes', 'proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $orden = OrdenCompra::create([
            'id_empleado' => auth()->user()->empleado->id_empleado,
            'id_proveedor' => $request->id_proveedor,
            'fecha_emision' => now(),
            'estado' => 'Borrador',
            'total_estimado' => 0,
        ]);

        $total = 0;
        foreach ($request->productos as $item) {
            $producto = Producto::find($item['id_producto']);
            
            DetalleOrden::create([
                'id_orden' => $orden->id_orden,
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
            ]);

            $total += $producto->precio_venta * $item['cantidad'];
        }

        $orden->total_estimado = $total;
        $orden->save();

        return redirect()->back()->with('success', 'Orden de compra creada.');
    }

    public function enviar(int $id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->enviarAProveedor();

        return redirect()->back()->with('success', 'Orden enviada al proveedor.');
    }

    public function recibir(int $id)
    {
        $orden = OrdenCompra::findOrFail($id);
        $orden->estado = 'Recibida';
        $orden->save();

        return redirect()->back()->with('success', 'Orden marcada como recibida.');
    }
}