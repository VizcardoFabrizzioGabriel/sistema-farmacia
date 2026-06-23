<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CarritoService
{
    private string $sessionKey = 'carrito_venta';

    // Obtener carrito de la sesión
    public function obtenerCarrito(): array
    {
        return Session::get($this->sessionKey, []);
    }

    // Agregar producto al carrito
    public function agregarProducto(int $idProducto, int $cantidad, float $precioUnitario, string $nombre): array
    {
        $carrito = $this->obtenerCarrito();

        if (isset($carrito[$idProducto])) {
            $carrito[$idProducto]['cantidad'] += $cantidad;
        } else {
            $carrito[$idProducto] = [
                'id_producto' => $idProducto,
                'nombre' => $nombre,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $precioUnitario * $cantidad,
            ];
        }

        // Recalcular subtotal
        $carrito[$idProducto]['subtotal'] = $carrito[$idProducto]['cantidad'] * $precioUnitario;

        Session::put($this->sessionKey, $carrito);
        return $carrito;
    }

    // Quitar producto del carrito
    public function quitarProducto(int $idProducto): array
    {
        $carrito = $this->obtenerCarrito();
        unset($carrito[$idProducto]);
        Session::put($this->sessionKey, $carrito);
        return $carrito;
    }

    // Actualizar cantidad
    public function actualizarCantidad(int $idProducto, int $cantidad): array
    {
        $carrito = $this->obtenerCarrito();
        
        if (isset($carrito[$idProducto])) {
            $carrito[$idProducto]['cantidad'] = $cantidad;
            $carrito[$idProducto]['subtotal'] = $cantidad * $carrito[$idProducto]['precio_unitario'];
            Session::put($this->sessionKey, $carrito);
        }

        return $carrito;
    }

    // Vaciar carrito
    public function vaciarCarrito(): void
    {
        Session::forget($this->sessionKey);
    }

    // Calcular totales del carrito
    public function calcularTotales(): array
    {
        $carrito = $this->obtenerCarrito();
        $subtotal = 0;

        foreach ($carrito as $item) {
            $subtotal += $item['subtotal'];
        }

        $impuestos = $subtotal * 0.18;
        $total = $subtotal + $impuestos;

        return [
            'items' => $carrito,
            'subtotal' => round($subtotal, 2),
            'impuestos' => round($impuestos, 2),
            'total' => round($total, 2),
            'cantidad_items' => count($carrito),
        ];
    }

    // Verificar si hay productos controlados en el carrito
    public function tieneControlados(): bool
    {
        $carrito = $this->obtenerCarrito();
        $idsProductos = array_keys($carrito);
        
        return \App\Models\Producto::whereIn('id_producto', $idsProductos)
            ->where(function ($query) {
                $query->where('es_controlado', true)
                      ->orWhere('requiere_receta', true);
            })
            ->exists();
    }
}