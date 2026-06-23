<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'id_empleado';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'fecha_ingreso',
        'turno',
        'numero_colegiatura',
        'caja_asignada',
        'cargo',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_empleado', 'id_empleado');
    }

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_empleado', 'id_empleado');
    }
}