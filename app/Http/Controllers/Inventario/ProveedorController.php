<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::withCount('lotes')->paginate(15);
        return view('dashboards.almacen.proveedores', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|string|max:20|unique:proveedores,ruc',
            'razon_social' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        Proveedor::create($request->all());

        return redirect()->back()->with('success', 'Proveedor registrado.');
    }

    public function update(Request $request, int $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'razon_social' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
        ]);

        $proveedor->update($request->all());

        return redirect()->back()->with('success', 'Proveedor actualizado.');
    }

    public function destroy(int $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        if ($proveedor->lotes()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: tiene lotes asociados.');
        }

        $proveedor->delete();
        return redirect()->back()->with('success', 'Proveedor eliminado.');
    }
}