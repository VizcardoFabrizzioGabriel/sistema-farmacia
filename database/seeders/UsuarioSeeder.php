<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // ADMINISTRATIVO (id_rol = 1)
        $idAdmin = DB::table('usuarios')->insertGetId([
            'id_rol' => 1,
            'dni' => '12345678',
            'nombres' => 'Carlos',
            'apellidos' => 'García López',
            'email' => 'admin@eddufarma.com',
            'password_hash' => Hash::make('password123'),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('empleados')->insert([
            'id_usuario' => $idAdmin,
            'fecha_ingreso' => '2023-01-15',
            'turno' => 'Mañana',
            'cargo' => 'Gerente General',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // FARMACÉUTICO (id_rol = 2)
        $idFarm = DB::table('usuarios')->insertGetId([
            'id_rol' => 2,
            'dni' => '87654321',
            'nombres' => 'María',
            'apellidos' => 'Rodríguez Pérez',
            'email' => 'farmaceutico@eddufarma.com',
            'password_hash' => Hash::make('password123'),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('empleados')->insert([
            'id_usuario' => $idFarm,
            'fecha_ingreso' => '2023-02-01',
            'turno' => 'Mañana',
            'numero_colegiatura' => 'CMP-12345',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // TÉCNICO FARMACÉUTICO (id_rol = 3)
        $idTecnico = DB::table('usuarios')->insertGetId([
            'id_rol' => 3,
            'dni' => '45678912',
            'nombres' => 'Luis',
            'apellidos' => 'Martínez Torres',
            'email' => 'tecnico@eddufarma.com',
            'password_hash' => Hash::make('password123'),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('empleados')->insert([
            'id_usuario' => $idTecnico,
            'fecha_ingreso' => '2023-03-10',
            'turno' => 'Tarde',
            'caja_asignada' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ENCARGADO DE ALMACÉN (id_rol = 4)
        $idAlmacen = DB::table('usuarios')->insertGetId([
            'id_rol' => 4,
            'dni' => '78912345',
            'nombres' => 'Ana',
            'apellidos' => 'Fernández Castro',
            'email' => 'almacen@eddufarma.com',
            'password_hash' => Hash::make('password123'),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('empleados')->insert([
            'id_usuario' => $idAlmacen,
            'fecha_ingreso' => '2023-04-05',
            'turno' => 'Mañana',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // CLIENTE (id_rol = 5)
        $idCliente = DB::table('usuarios')->insertGetId([
            'id_rol' => 5,
            'dni' => '11111111',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez Gómez',
            'email' => 'cliente@eddufarma.com',
            'password_hash' => Hash::make('password123'),
            'estado' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('clientes')->insert([
            'id_usuario' => $idCliente,
            'puntos_fidelidad' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}