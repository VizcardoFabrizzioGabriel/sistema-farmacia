<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PasarelaPagoService;

class StripeController extends Controller
{
    private PasarelaPagoService $pasarela;

    public function __construct()
    {
        $this->pasarela = new PasarelaPagoService();
    }

    public function procesarPago(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'venta_id' => 'required|integer',
        ]);

        $resultado = $this->pasarela->confirmarPago($request->payment_intent_id);

        if ($resultado['success']) {
            $venta = \App\Models\Venta::findOrFail($request->venta_id);
            $venta->stripe_payment_id = $request->payment_intent_id;
            $venta->estado = 'Pagada';
            $venta->save();

            return response()->json([
                'success' => true,
                'message' => 'Pago confirmado.',
                'venta' => $venta,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $resultado['message'],
        ]);
    }

    public function crearIntent(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:1',
        ]);

        $resultado = $this->pasarela->crearPaymentIntent($request->monto);

        return response()->json($resultado);
    }
}