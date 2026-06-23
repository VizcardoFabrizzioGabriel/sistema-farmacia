<?php

namespace App\Http\Controllers\Venta;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Comprobante;
use App\Services\FacturacionService;

class ComprobanteController extends Controller
{
    private FacturacionService $facturacion;

    public function __construct()
    {
        $this->facturacion = new FacturacionService();
    }

    public function generar(int $idVenta)
    {
        $venta = Venta::with(['detalles.producto', 'cliente.usuario', 'empleado.usuario'])
            ->findOrFail($idVenta);

        if ($venta->estado !== 'Pagada') {
            return back()->with('error', 'Solo se pueden generar comprobantes de ventas pagadas.');
        }

        if ($venta->comprobante) {
            return redirect()->route('tecnico.comprobante.ver', $venta->comprobante->id_comprobante);
        }

        $tipo = request()->get('tipo', 'Boleta');
        $comprobante = $this->facturacion->generarComprobante($venta, $tipo);

        return redirect()->route('tecnico.comprobante.ver', $comprobante->id_comprobante)
            ->with('success', 'Comprobante generado correctamente.');
    }

    public function ver(int $idComprobante)
    {
        $comprobante = Comprobante::with(['venta.detalles.producto', 'venta.cliente.usuario', 'venta.empleado.usuario'])
            ->findOrFail($idComprobante);

        $venta = $comprobante->venta;

        return view('ventas.comprobante', compact('comprobante', 'venta'));
    }

    public function descargarPDF(int $idComprobante)
    {
        $comprobante = Comprobante::findOrFail($idComprobante);

        if (!$comprobante->url_pdf) {
            return back()->with('error', 'El PDF aún no ha sido generado.');
        }

        $path = storage_path('app/public/' . $comprobante->url_pdf);

        if (!file_exists($path)) {
            return back()->with('error', 'Archivo PDF no encontrado.');
        }

        return response()->download($path, $comprobante->numero_serie . '.pdf');
    }
}