<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'Administrativo', 'descripcion' => 'Gestión comercial, finanzas, usuarios y reportes'],
            ['nombre' => 'Farmaceutico', 'descripcion' => 'Validación de recetas, medicamentos controlados'],
            ['nombre' => 'TecnicoFarmaceutico', 'descripcion' => 'Atención en mostrador, ventas OTC'],
            ['nombre' => 'EncargadoAlmacen', 'descripcion' => 'Logística, lotes, proveedores e inventario'],
            ['nombre' => 'Cliente', 'descripcion' => 'Usuario del sistema transaccional'],
        ]);
    }
}