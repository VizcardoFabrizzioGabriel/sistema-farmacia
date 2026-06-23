<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EncargadoAlmacen extends Empleado
{
    use HasFactory;

    protected $table = 'empleados';

    /**
     * Boot del modelo para filtrar solo encargados de almacen
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('encargado_almacen', function ($query) {
            $query->whereHas('usuario', function ($q) {
                $q->where('id_rol', 4); // Rol EncargadoAlmacen
            });
        });
    }

    // Métodos específicos del Encargado de Almacen
    public function registrarLote(array $datos): Lote
    {
        return Lote::create($datos);
    }

    public function generarOrdenCompra(int $idProveedor, array $productos): OrdenCompra
    {
        $orden = OrdenCompra::create([
            'id_empleado' => $this->id_empleado,
            'id_proveedor' => $idProveedor,
            'fecha_emision' => now(),
            'estado' => 'Borrador',
            'total_estimado' => 0,
        ]);

        $total = 0;
        foreach ($productos as $item) {
            $producto = Producto::find($item['id_producto']);
            DetalleOrden::create([
                'id_orden' => $orden->id_orden,
                'id_producto' => $item['id_producto'],
                'cantidad' => $item['cantidad'],
            ]);
            $total += $producto->precio_venta * $item['cantidad'];
        }

        $orden->total_estimado = $total;
        $orden->save();

        return $orden;
    }

    public function auditarInventario(): array
    {
        $service = new \App\Services\InventarioService();
        return $service->auditarInventario();
    }

    public function gestionarCuarentena(int $idLote, string $nuevoEstado): Lote
    {
        $lote = Lote::findOrFail($idLote);
        $lote->estado = $nuevoEstado;
        $lote->save();
        $lote->producto->actualizarStock();

        return $lote;
    }
}