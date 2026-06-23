<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StripeController;
use App\Http\Controllers\API\GeminiController;
use App\Http\Controllers\API\TelegramController;
use App\Http\Controllers\API\MapaController;
use App\Http\Controllers\Venta\VentaController;
use App\Http\Controllers\Receta\RecetaMedicaController;
use App\Http\Controllers\Inventario\ProductoController;

// ============================================
// API REST EDDUFARMA
// ============================================

// Stripe - Pagos
Route::post('/stripe/crear-intent', [StripeController::class, 'crearIntent']);
Route::post('/stripe/confirmar', [StripeController::class, 'procesarPago']);

// Gemini - Asistente IA
Route::post('/gemini/consultar', [GeminiController::class, 'consultar']);
Route::post('/gemini/interacciones', [GeminiController::class, 'interacciones']);
Route::post('/gemini/info-producto', [GeminiController::class, 'infoProducto']);

// Telegram - Webhook
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// Mapa - Proveedores
Route::get('/mapa/proveedores', [MapaController::class, 'listarProveedores']);

// Productos disponibles (público)
Route::get('/productos/disponibles', [ProductoController::class, 'apiDisponibles']);

// Consultar disponibilidad de producto
Route::get('/productos/{id}/disponibilidad', [VentaController::class, 'consultarDisponibilidad']);

// Validar receta
Route::post('/recetas/validar', [RecetaMedicaController::class, 'validar']);