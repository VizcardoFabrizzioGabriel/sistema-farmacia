<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramBotService
{
    private string $token;
    private string $chatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function enviarMensaje(string $mensaje): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        try {
            $response = Http::post($url, [
                'chat_id' => $this->chatId,
                'text' => $mensaje,
                'parse_mode' => 'HTML',
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function notificarStockBajo(string $nombreProducto, int $stockActual, int $stockMinimo): void
    {
        $mensaje = "⚠️ <b>ALERTA DE STOCK BAJO</b>\n\n";
        $mensaje .= "Producto: <b>{$nombreProducto}</b>\n";
        $mensaje .= "Stock actual: <b>{$stockActual}</b>\n";
        $mensaje .= "Stock mínimo: <b>{$stockMinimo}</b>\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i');

        $this->enviarMensaje($mensaje);
    }

    public function notificarCaducidad(string $numeroLote, string $nombreProducto, string $fechaVencimiento, string $estado): void
    {
        $emoji = $estado === 'Cuarentena' ? '🚨' : '⚠️';
        $mensaje = "{$emoji} <b>ALERTA DE CADUCIDAD</b>\n\n";
        $mensaje .= "Lote: <b>{$numeroLote}</b>\n";
        $mensaje .= "Producto: <b>{$nombreProducto}</b>\n";
        $mensaje .= "Fecha vencimiento: <b>{$fechaVencimiento}</b>\n";
        $mensaje .= "Estado: <b>{$estado}</b>\n";
        $mensaje .= "Fecha alerta: " . now()->format('d/m/Y H:i');

        $this->enviarMensaje($mensaje);
    }

    public function notificarNuevaVenta(float $total, string $empleado): void
    {
        $mensaje = "💰 <b>NUEVA VENTA REGISTRADA</b>\n\n";
        $mensaje .= "Total: <b>S/ " . number_format($total, 2) . "</b>\n";
        $mensaje .= "Atendido por: <b>{$empleado}</b>\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i');

        $this->enviarMensaje($mensaje);
    }
}