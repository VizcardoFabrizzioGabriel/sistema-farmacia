<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalles_venta';
    protected $primaryKey = 'id_detalle';
    public $timestamps = true;

    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // Método: calcular subtotal
    public function calcularSubtotal()
    {
        $this->subtotal = ($this->precio_unitario * $this->cantidad) - $this->descuento;
        $this->save();
        return $this->subtotal;
    }
}