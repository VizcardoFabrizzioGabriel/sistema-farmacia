<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = true;

    protected $fillable = [
        'ruc',
        'razon_social',
        'contacto',
        'telefono',
        'latitud',
        'longitud',
    ];

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'id_proveedor', 'id_proveedor');
    }

    public function ordenesCompra()
    {
        return $this->hasMany(OrdenCompra::class, 'id_proveedor', 'id_proveedor');
    }
}