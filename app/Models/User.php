<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'id_rol',
        'dni',
        'nombres',
        'apellidos',
        'email',
        'password_hash',
        'estado',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Laravel usa 'password' por defecto, mapeamos a nuestra columna
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'id_usuario', 'id_usuario');
    }

    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'id_usuario', 'id_usuario');
    }

    // Helper para verificar rol
    public function tieneRol($nombreRol)
    {
        return $this->rol && $this->rol->nombre === $nombreRol;
    }
}