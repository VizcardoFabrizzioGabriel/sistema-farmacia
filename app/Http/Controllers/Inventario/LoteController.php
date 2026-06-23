<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use App\Models\Lote;
use App\Models\Producto;
use App\Models\Proveedor;

class LoteController extends Controller
{
    // Listar lotes
    public function index()
    {
        $lotes = Lote::with(['producto', 'proveedor'])
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(20);

        return view('dashboards.almacen.lotes', compact('lotes'));
    }

    // Registrar nuevo lote
    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'numero_lote' => 'required|string|max:50',
            'fecha_fabricacion' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_fabricacion',
            'cantidad_inicial' => 'required|integer|min:1',
        ]);

        $lote = Lote::create([
            'id_producto' => $request->id_producto,
            'id_proveedor' => $request->id_proveedor,
            'numero_lote' => $request->numero_lote,
            'fecha_fabricacion' => $request->fecha_fabricacion,
            'fecha_vencimiento' => $request->fecha_vencimiento,
            'cantidad_inicial' => $request->cantidad_inicial,
            'cantidad_actual' => $request->cantidad_inicial,
            'estado' => 'Activo',
        ]);

        // Actualizar stock del producto
        $producto = Producto::find($request->id_producto);
        $producto->actualizarStock();

        return redirect()->back()->with('success', 'Lote registrado correctamente.');
    }

    // Cambiar estado de lote (Cuarentena, Baja)
    public function cambiarEstado(Request $request, int $id)
    {
        $lote = Lote::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:Activo,PorVencer,Cuarentena,Baja',
        ]);

        $lote->estado = $request->estado;
        $lote->save();

        $lote->producto->actualizarStock();

        return redirect()->back()->with('success', 'Estado del lote actualizado.');
    }

    // Ordenar lotes con Heapsort (Python)
    public function ordenarHeapsort(Request $request)
    {
        $criterio = $request->get('criterio', 'fecha_vencimiento');

        $lotes = Lote::with('producto')->get()->map(function ($lote) {
            return [
                'id_lote' => $lote->id_lote,
                'numero_lote' => $lote->numero_lote,
                'nombre_producto' => $lote->producto->nombre,
                'fecha_vencimiento' => $lote->fecha_vencimiento->format('Y-m-d'),
                'cantidad_actual' => $lote->cantidad_actual,
                'estado' => $lote->estado,
            ];
        })->toArray();

        $jsonData = json_encode($lotes);
        $pythonPath = base_path('python/heapsort_inventario.py');

        $resultado = Process::run("python3 {$pythonPath} {$criterio} '{$jsonData}'");

        if ($resultado->successful()) {
            $output = json_decode($resultado->output(), true);
            return view('dashboards.almacen.lotes', [
                'lotes' => Lote::with(['producto', 'proveedor'])->paginate(20),
                'lotesOrdenados' => $output['data'] ?? [],
                'criterio' => $criterio,
            ]);
        }

        return redirect()->back()->with('error', 'Error al ejecutar Heapsort: ' . $resultado->errorOutput());
    }
}