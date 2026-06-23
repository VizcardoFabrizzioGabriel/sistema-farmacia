<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $table = 'detalles_orden';
    protected $primaryKey = 'id_detalle_orden';
    public $timestamps = true;

    protected $fillable = [
        'id_orden',
        'id_producto',
        'cantidad',
    ];

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'id_orden', 'id_orden');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}