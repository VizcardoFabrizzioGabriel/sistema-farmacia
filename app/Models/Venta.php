<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    public $timestamps = true;

    protected $fillable = [
        'id_cliente',
        'id_empleado',
        'id_receta',
        'fecha_hora',
        'subtotal',
        'impuestos',
        'total',
        'metodo_pago',
        'estado',
        'stripe_payment_id',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    public function recetaMedica()
    {
        return $this->belongsTo(RecetaMedica::class, 'id_receta', 'id_receta');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public function comprobante()
    {
        return $this->hasOne(Comprobante::class, 'id_venta', 'id_venta');
    }

    // Método: calcular total
    public function calcularTotal()
    {
        $this->subtotal = $this->detalles->sum('subtotal');
        $this->impuestos = $this->subtotal * 0.18; // IGV 18%
        $this->total = $this->subtotal + $this->impuestos;
        $this->save();
        return $this;
    }

    // Método: finalizar transacción
    public function finalizarTransaccion()
    {
        if ($this->estado === 'Validando') {
            $this->estado = 'Pagada';
            $this->save();
        }
        return $this;
    }

    // Método: anular venta (mismo día)
    public function anular()
    {
        $hoy = now()->format('Y-m-d');
        $fechaVenta = $this->fecha_hora->format('Y-m-d');
        
        if ($hoy === $fechaVenta && in_array($this->estado, ['Pendiente', 'Pagada'])) {
            // Devolver stock
            foreach ($this->detalles as $detalle) {
                $lotes = Lote::where('id_producto', $detalle->id_producto)
                    ->whereIn('estado', ['Activo', 'PorVencer', 'Cuarentena'])
                    ->orderBy('fecha_vencimiento', 'desc')
                    ->get();
                
                $cantidadRestante = $detalle->cantidad;
                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;
                    $lote->cantidad_actual += min($cantidadRestante, $lote->cantidad_inicial - $lote->cantidad_actual + $lote->cantidad_actual);
                    $lote->save();
                    $cantidadRestante -= $lote->cantidad_actual;
                }
                $detalle->producto->actualizarStock();
            }
            
            $this->estado = 'Anulada';
            $this->save();
            return true;
        }
        return false;
    }
}