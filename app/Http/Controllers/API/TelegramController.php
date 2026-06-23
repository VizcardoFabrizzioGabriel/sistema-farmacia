<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TelegramBotService;

class TelegramController extends Controller
{
    private TelegramBotService $telegram;

    public function __construct()
    {
        $this->telegram = new TelegramBotService();
    }

    // Webhook para recibir mensajes de Telegram
    public function webhook(Request $request)
    {
        $data = $request->all();

        if (isset($data['message'])) {
            $chatId = $data['message']['chat']['id'];
            $texto = $data['message']['text'] ?? '';

            // Comandos del bot
            switch ($texto) {
                case '/start':
                    $this->telegram->enviarMensaje("🤖 <b>Bienvenido a EDDUFARMA Bot</b>\n\nComandos disponibles:\n/stock - Ver productos con stock bajo\n/ventas - Ver ventas de hoy\n/ayuda - Información de ayuda");
                    break;

                case '/stock':
                    $productos = \App\Models\Producto::whereColumn('stock_actual', '<=', 'stock_minimo')->get();
                    $mensaje = "📦 <b>Productos con stock bajo:</b>\n\n";
                    foreach ($productos as $p) {
                        $mensaje .= "• {$p->nombre}: {$p->stock_actual}/{$p->stock_minimo}\n";
                    }
                    $this->telegram->enviarMensaje($mensaje);
                    break;

                case '/ventas':
                    $ventasHoy = \App\Models\Venta::whereDate('fecha_hora', today())
                        ->where('estado', 'Pagada')
                        ->sum('total');
                    $cantidad = \App\Models\Venta::whereDate('fecha_hora', today())
                        ->where('estado', 'Pagada')
                        ->count();
                    $mensaje = "💰 <b>Ventas de hoy:</b>\nCantidad: {$cantidad}\nTotal: S/ " . number_format($ventasHoy, 2);
                    $this->telegram->enviarMensaje($mensaje);
                    break;

                case '/ayuda':
                    $this->telegram->enviarMensaje("ℹ️ <b>Ayuda EDDUFARMA</b>\n\nEste bot te notifica sobre:\n• Stock bajo\n• Caducidad de lotes\n• Ventas diarias\n\nContacto: admin@eddufarma.com");
                    break;

                default:
                    $this->telegram->enviarMensaje("❓ Comando no reconocido. Usa /ayuda para ver los comandos disponibles.");
            }
        }

        return response()->json(['status' => 'ok']);
    }

    // Enviar mensaje manual
    public function enviarMensaje(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string',
        ]);

        $resultado = $this->telegram->enviarMensaje($request->mensaje);

        return response()->json($resultado);
    }
}