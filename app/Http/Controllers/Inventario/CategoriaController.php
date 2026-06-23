<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->get();
        return view('dashboards.admin.categorias', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Categoria::create($request->all());

        return redirect()->back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($request->all());

        return redirect()->back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(int $id)
    {
        $categoria = Categoria::findOrFail($id);
        
        if ($categoria->productos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar: tiene productos asociados.');
        }

        $categoria->delete();
        return redirect()->back()->with('success', 'Categoría eliminada.');
    }
}