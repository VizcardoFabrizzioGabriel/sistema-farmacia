<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Venta;

class PasarelaPagoService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function crearPaymentIntent(float $monto, string $moneda = 'pen'): array
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($monto * 100), // Stripe usa centavos
                'currency' => $moneda,
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function confirmarPago(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'estado' => 'Pagada',
                    'transaccion_id' => $paymentIntent->id,
                ];
            }

            return [
                'success' => false,
                'estado' => $paymentIntent->status,
                'message' => 'El pago no fue completado.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function procesarReembolso(Venta $venta): array
    {
        if (!$venta->stripe_payment_id) {
            return [
                'success' => false,
                'message' => 'Esta venta no tiene pago con Stripe.',
            ];
        }

        try {
            $refund = \Stripe\Refund::create([
                'payment_intent' => $venta->stripe_payment_id,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'message' => 'Reembolso procesado correctamente.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}