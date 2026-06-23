<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'ordenes_compra';
    protected $primaryKey = 'id_orden';
    public $timestamps = true;

    protected $fillable = [
        'id_empleado',
        'id_proveedor',
        'fecha_emision',
        'estado',
        'total_estimado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'id_orden', 'id_orden');
    }

    // Método: agregar producto
    public function agregarProducto($idProducto, $cantidad, $precioUnitario)
    {
        $detalle = DetalleOrden::create([
            'id_orden' => $this->id_orden,
            'id_producto' => $idProducto,
            'cantidad' => $cantidad,
        ]);
        
        $this->total_estimado += ($cantidad * $precioUnitario);
        $this->save();
        
        return $detalle;
    }

    // Método: enviar a proveedor
    public function enviarAProveedor()
    {
        $this->estado = 'Enviada';
        $this->save();
        return $this;
    }
}