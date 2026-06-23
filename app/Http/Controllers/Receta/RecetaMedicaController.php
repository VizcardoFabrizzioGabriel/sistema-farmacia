<?php

namespace App\Http\Controllers\Receta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecetaMedica;
use App\Models\Venta;
use App\Services\RecetaService;
use App\Services\FacturacionService;
use App\Services\TelegramBotService;

class RecetaMedicaController extends Controller
{
    private RecetaService $recetaService;
    private FacturacionService $facturacion;
    private TelegramBotService $telegram;

    public function __construct()
    {
        $this->recetaService = new RecetaService();
        $this->facturacion = new FacturacionService();
        $this->telegram = new TelegramBotService();
    }

    // Vista: Listar recetas pendientes (Farmacéutico)
    public function index()
    {
        $recetas = RecetaMedica::with(['venta.detalles.producto', 'venta.empleado.usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboards.farmaceutico.validar_recetas', compact('recetas'));
    }

    // Crear receta desde el POS (Técnico)
    public function store(Request $request)
    {
        $request->validate([
            'codigo_receta' => 'required|string|unique:recetas_medicas,codigo_receta',
            'dni_medico' => 'required|string|max:15',
            'nombre_medico' => 'required|string|max:150',
            'fecha_emision' => 'required|date',
            'url_imagen' => 'nullable|string',
        ]);

        $receta = $this->recetaService->crearReceta($request->all());

        return response()->json([
            'success' => true,
            'receta' => $receta,
            'message' => 'Receta registrada. Pendiente de validación.',
        ]);
    }

    // Aprobar receta (Farmacéutico)
    public function aprobar(int $idReceta)
    {
        $resultado = $this->recetaService->aprobarReceta($idReceta);

        if ($resultado['success']) {
            // Si hay venta asociada, completarla
            $venta = Venta::where('id_receta', $idReceta)->first();
            if ($venta && $venta->estado === 'Validando') {
                $venta->estado = 'Pagada';
                $venta->save();

                $this->facturacion->generarComprobante($venta, 'Boleta');

                $this->telegram->notificarNuevaVenta(
                    $venta->total,
                    $venta->empleado->usuario->nombres . ' ' . $venta->empleado->usuario->apellidos
                );
            }

            return redirect()->back()->with('success', 'Receta aprobada y venta completada.');
        }

        return redirect()->back()->with('error', 'Error al aprobar receta.');
    }

    // Rechazar receta
    public function rechazar(int $idReceta)
    {
        $receta = RecetaMedica::findOrFail($idReceta);
        
        $venta = Venta::where('id_receta', $receta->id_receta)->first();
        if ($venta) {
            // Devolver stock
            foreach ($venta->detalles as $detalle) {
                $lotes = \App\Models\Lote::where('id_producto', $detalle->id_producto)
                    ->orderBy('fecha_vencimiento', 'desc')
                    ->get();
                
                $cantidadRestante = $detalle->cantidad;
                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;
                    $lote->cantidad_actual += min($cantidadRestante, $detalle->cantidad);
                    $lote->save();
                    $cantidadRestante -= $lote->cantidad_actual;
                }
                $detalle->producto->actualizarStock();
            }
            
            $venta->estado = 'Anulada';
            $venta->save();
        }

        return redirect()->back()->with('success', 'Receta rechazada. Venta anulada.');
    }

    // Validar receta vía API (desde el POS)
    public function validar(Request $request)
    {
        $request->validate([
            'codigo_receta' => 'required|string',
            'dni_medico' => 'required|string',
        ]);

        $resultado = $this->recetaService->validarReceta(
            $request->codigo_receta,
            $request->dni_medico
        );

        return response()->json($resultado);
    }
}