<?php

namespace App\Http\Controllers\Venta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use App\Models\RecetaMedica;
use App\Services\CarritoService;
use App\Services\FEFOService;
use App\Services\FacturacionService;
use App\Services\PasarelaPagoService;
use App\Services\RecetaService;
use App\Services\TelegramBotService;
use Carbon\Carbon;

class VentaController extends Controller
{
    private CarritoService $carrito;
    private FEFOService $fefo;
    private FacturacionService $facturacion;
    private PasarelaPagoService $pasarela;
    private RecetaService $recetaService;
    private TelegramBotService $telegram;

    public function __construct()
    {
        $this->carrito = new CarritoService();
        $this->fefo = new FEFOService();
        $this->facturacion = new FacturacionService();
        $this->pasarela = new PasarelaPagoService();
        $this->recetaService = new RecetaService();
        $this->telegram = new TelegramBotService();
    }

    // Vista POS (Punto de Venta)
    public function create()
    {
        $productos = Producto::where('stock_actual', '>', 0)->get();
        $carrito = $this->carrito->calcularTotales();
        return view('dashboards.tecnico.dispensar', compact('productos', 'carrito'));
    }

    // Consultar disponibilidad de producto (API)
    public function consultarDisponibilidad(int $idProducto)
    {
        $producto = Producto::with('lotes')->findOrFail($idProducto);

        return response()->json([
            'id' => $producto->id_producto,
            'nombre' => $producto->nombre,
            'precio' => $producto->precio_venta,
            'stock_actual' => $producto->stock_actual,
            'es_controlado' => $producto->es_controlado,
            'requiere_receta' => $producto->requiere_receta,
            'lotes_fefo' => $this->fefo->obtenerLotesFEFO($idProducto),
        ]);
    }

    // Procesar venta completa
    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|string',
            'tipo_comprobante' => 'required|in:Boleta,Factura',
        ]);

        $carrito = $this->carrito->calcularTotales();

        if ($carrito['cantidad_items'] === 0) {
            return back()->with('error', 'El carrito está vacío.');
        }

        // Verificar si requiere receta
        $idsProductos = array_column($carrito['items'], 'id_producto');
        $requiereReceta = $this->recetaService->requiereReceta($idsProductos);

        DB::beginTransaction();

        try {
            // Crear venta
            $venta = Venta::create([
                'id_cliente' => auth()->user()->cliente?->id_cliente,
                'id_empleado' => auth()->user()->empleado->id_empleado,
                'id_receta' => $request->id_receta ?? null,
                'fecha_hora' => now(),
                'subtotal' => $carrito['subtotal'],
                'impuestos' => $carrito['impuestos'],
                'total' => $carrito['total'],
                'metodo_pago' => $request->metodo_pago,
                'estado' => $requiereReceta ? 'Validando' : 'Pagada',
            ]);

            // Crear detalles y descontar stock FEFO
            foreach ($carrito['items'] as $item) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento' => 0,
                    'subtotal' => $item['subtotal'],
                ]);

                // Descuento FEFO
                $this->fefo->descontarStockFEFO($item['id_producto'], $item['cantidad']);
            }

            // Si no requiere receta, generar comprobante
            if (!$requiereReceta) {
                $comprobante = $this->facturacion->generarComprobante($venta, $request->tipo_comprobante);
                $venta->estado = 'Pagada';
                $venta->save();

                // Notificar por Telegram
                $this->telegram->notificarNuevaVenta(
                    $venta->total,
                    auth()->user()->nombres . ' ' . auth()->user()->apellidos
                );
            }

            DB::commit();

            $this->carrito->vaciarCarrito();

            if ($requiereReceta) {
                return redirect()->route('tecnico.receta', $venta->id_venta)
                    ->with('warning', 'Venta en validación. Se requiere aprobación del farmacéutico.');
            }

            return redirect()->route('tecnico.ticket', $venta->id_venta)
                ->with('success', 'Venta procesada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    // Mostrar ticket
    public function ticket(int $idVenta)
    {
        $venta = Venta::with(['detalles.producto', 'empleado.usuario', 'cliente.usuario', 'comprobante'])
            ->findOrFail($idVenta);

        $ticket = $this->facturacion->generarTicket($venta);

        return view('ventas.ticket', compact('venta', 'ticket'));
    }

    // Mostrar vista de receta pendiente
    public function receta(int $idVenta)
    {
        $venta = Venta::with(['detalles.producto', 'recetaMedica'])->findOrFail($idVenta);
        return view('recetas.validar', compact('venta'));
    }

    // Historial de ventas (cliente)
    public function historial()
    {
        $cliente = auth()->user()->cliente;
        $ventas = Venta::where('id_cliente', $cliente->id_cliente)
            ->where('estado', 'Pagada')
            ->orderBy('fecha_hora', 'desc')
            ->paginate(10);

        return view('dashboards.cliente.historial', compact('ventas'));
    }

    // Seguimiento de pedido
    public function seguimiento()
    {
        $cliente = auth()->user()->cliente;
        $ventas = Venta::where('id_cliente', $cliente->id_cliente)
            ->orderBy('fecha_hora', 'desc')
            ->take(5)
            ->get();

        return view('dashboards.cliente.seguimiento', compact('ventas'));
    }

    // Ventas anulables (para farmacéutico)
    public function anulables()
    {
        $ventas = Venta::whereDate('fecha_hora', today())
            ->where('estado', 'Pagada')
            ->with(['detalles.producto', 'cliente.usuario', 'empleado.usuario'])
            ->get();

        return view('dashboards.farmaceutico.anular_ventas', compact('ventas'));
    }

    // Anular venta
    public function anular(int $idVenta)
    {
        $venta = Venta::findOrFail($idVenta);

        if ($venta->anular()) {
            return redirect()->back()->with('success', 'Venta anulada correctamente. Stock devuelto.');
        }

        return redirect()->back()->with('error', 'No se puede anular esta venta. Solo se permiten anulaciones el mismo día.');
    }

    // API: Crear PaymentIntent para Stripe
    public function crearPaymentIntent(Request $request)
    {
        $carrito = $this->carrito->calcularTotales();
        $resultado = $this->pasarela->crearPaymentIntent($carrito['total']);

        return response()->json($resultado);
    }
}