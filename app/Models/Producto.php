<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = true;

    protected $fillable = [
        'id_categoria',
        'codigo_barras',
        'nombre',
        'descripcion',
        'precio_venta',
        'es_controlado',
        'requiere_receta',
        'stock_actual',
        'stock_minimo',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_producto', 'id_producto');
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }

    // Método: consultar disponibilidad
    public function consultarDisponibilidad()
    {
        return $this->stock_actual > 0 && $this->lotes()->where('estado', 'Activo')->sum('cantidad_actual') > 0;
    }

    // Método: actualizar stock desde lotes activos
    public function actualizarStock()
    {
        $this->stock_actual = $this->lotes()
            ->whereIn('estado', ['Activo', 'PorVencer'])
            ->sum('cantidad_actual');
        $this->save();
        return $this->stock_actual;
    }
}