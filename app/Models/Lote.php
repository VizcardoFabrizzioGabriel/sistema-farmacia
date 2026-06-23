<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';
    protected $primaryKey = 'id_lote';
    public $timestamps = true;

    protected $fillable = [
        'id_producto',
        'id_proveedor',
        'numero_lote',
        'fecha_fabricacion',
        'fecha_vencimiento',
        'cantidad_inicial',
        'cantidad_actual',
        'estado',
    ];

    protected $casts = [
        'fecha_fabricacion' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    // Método: descontar stock FEFO
    public function descontarStockFEFO($cantidad)
    {
        if ($this->cantidad_actual >= $cantidad) {
            $this->cantidad_actual -= $cantidad;
            if ($this->cantidad_actual == 0) {
                $this->estado = 'Baja';
            }
            $this->save();
            $this->producto->actualizarStock();
            return true;
        }
        return false;
    }

    // Método: evaluar estado de caducidad
    public function evaluarEstadoCaducidad()
    {
        $hoy = Carbon::now();
        $diasRestantes = $hoy->diffInDays($this->fecha_vencimiento, false);

        if ($diasRestantes <= 0) {
            $this->estado = 'Baja';
        } elseif ($diasRestantes <= 30) {
            $this->estado = 'Cuarentena';
        } elseif ($diasRestantes <= 90) {
            $this->estado = 'PorVencer';
        } else {
            $this->estado = 'Activo';
        }
        $this->save();
        return $this->estado;
    }
}