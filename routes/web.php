<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\FarmaceuticoDashboardController;
use App\Http\Controllers\Dashboard\TecnicoDashboardController;
use App\Http\Controllers\Dashboard\AlmacenDashboardController;
use App\Http\Controllers\Dashboard\ClienteDashboardController;
use App\Http\Controllers\Venta\VentaController;
use App\Http\Controllers\Venta\CarritoController;
use App\Http\Controllers\Inventario\ProductoController;
use App\Http\Controllers\Inventario\CategoriaController;
use App\Http\Controllers\Inventario\LoteController;
use App\Http\Controllers\Inventario\ProveedorController;
use App\Http\Controllers\Inventario\OrdenCompraController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Receta\RecetaMedicaController;
use App\Http\Controllers\API\MapaController;

// ============================================
// RUTAS PÚBLICAS
// ============================================

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ============================================
// ADMINISTRATIVO
// ============================================
Route::middleware(['auth', 'role:Administrativo'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    
    // Productos
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    
    // Categorías
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias');
    Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    
    // Reportes
    Route::get('/reportes', [\App\Http\Controllers\Reporte\ReporteController::class, 'index'])->name('reportes');
});

// ============================================
// FARMACÉUTICO
// ============================================
Route::middleware(['auth', 'role:Farmaceutico'])->prefix('farmaceutico')->name('farmaceutico.')->group(function () {
    Route::get('/', [FarmaceuticoDashboardController::class, 'index'])->name('dashboard');
    Route::get('/validar-recetas', [RecetaMedicaController::class, 'index'])->name('recetas');
    Route::post('/aprobar-receta/{id}', [RecetaMedicaController::class, 'aprobar'])->name('recetas.aprobar');
    Route::post('/rechazar-receta/{id}', [RecetaMedicaController::class, 'rechazar'])->name('recetas.rechazar');
    Route::get('/anular-ventas', [\App\Http\Controllers\Venta\VentaController::class, 'anulables'])->name('anular');
});

// ============================================
// TÉCNICO FARMACÉUTICO
// ============================================
Route::middleware(['auth', 'role:TecnicoFarmaceutico'])->prefix('tecnico')->name('tecnico.')->group(function () {
    Route::get('/', [TecnicoDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dispensar', [VentaController::class, 'create'])->name('dispensar');
    Route::post('/venta', [VentaController::class, 'store'])->name('venta.store');
    Route::get('/ticket/{id}', [VentaController::class, 'ticket'])->name('ticket');
    Route::get('/receta/{id}', [RecetaMedicaController::class, 'vistaReceta'])->name('receta');
    
    // Carrito AJAX
    Route::post('/carrito/agregar', [CarritoController::class, 'add'])->name('carrito.add');
    Route::post('/carrito/quitar', [CarritoController::class, 'remove'])->name('carrito.remove');
    Route::get('/carrito', [CarritoController::class, 'get'])->name('carrito.get');
    Route::delete('/carrito', [CarritoController::class, 'clear'])->name('carrito.clear');
});

// ============================================
// ENCARGADO DE ALMACÉN
// ============================================
Route::middleware(['auth', 'role:EncargadoAlmacen'])->prefix('almacen')->name('almacen.')->group(function () {
    Route::get('/', [AlmacenDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lotes', [LoteController::class, 'index'])->name('lotes');
    Route::post('/lotes', [LoteController::class, 'store'])->name('lotes.store');
    Route::put('/lotes/{id}/estado', [LoteController::class, 'cambiarEstado'])->name('lotes.estado');
    Route::get('/lotes/ordenar', [LoteController::class, 'ordenarHeapsort'])->name('lotes.ordenar');
    
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    
    Route::get('/ordenes-compra', [OrdenCompraController::class, 'index'])->name('ordenes');
    Route::post('/ordenes-compra', [OrdenCompraController::class, 'store'])->name('ordenes.store');
    Route::post('/ordenes-compra/{id}/enviar', [OrdenCompraController::class, 'enviar'])->name('ordenes.enviar');
    Route::post('/ordenes-compra/{id}/recibir', [OrdenCompraController::class, 'recibir'])->name('ordenes.recibir');
    
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa');
});

// ============================================
// CLIENTE
// ============================================
Route::middleware(['auth', 'role:Cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/', [ClienteDashboardController::class, 'index'])->name('dashboard');
    Route::get('/catalogo', [ProductoController::class, 'catalogo'])->name('catalogo');
    Route::get('/historial', [VentaController::class, 'historial'])->name('historial');
    Route::get('/seguimiento', [\App\Http\Controllers\Venta\VentaController::class, 'seguimiento'])->name('seguimiento');
});