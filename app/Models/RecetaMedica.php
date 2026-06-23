<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RecetaMedica extends Model
{
    use HasFactory;

    protected $table = 'recetas_medicas';
    protected $primaryKey = 'id_receta';
    public $timestamps = true;

    protected $fillable = [
        'codigo_receta',
        'dni_medico',
        'nombre_medico',
        'fecha_emision',
        'url_imagen',
        'estado_validacion',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'estado_validacion' => 'boolean',
    ];

    public function venta()
    {
        return $this->hasOne(Venta::class, 'id_receta', 'id_receta');
    }

    // Método: verificar vigencia (receta válida por 30 días)
    public function verificarVigencia()
    {
        $hoy = Carbon::now();
        $diasTranscurridos = $this->fecha_emision->diffInDays($hoy);
        
        return $diasTranscurridos <= 30 && $this->estado_validacion;
    }

    // Método: cargar imagen
    public function cargarImagen($ruta)
    {
        $this->url_imagen = $ruta;
        $this->save();
        return $this;
    }
}