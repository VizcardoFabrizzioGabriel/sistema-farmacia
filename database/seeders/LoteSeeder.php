<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoteSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = Carbon::now();

        DB::table('lotes')->insert([
            // Paracetamol - Activo
            [
                'id_producto' => 1,
                'id_proveedor' => 1,
                'numero_lote' => 'LOT-PARA-001',
                'fecha_fabricacion' => '2024-01-15',
                'fecha_vencimiento' => $hoy->copy()->addDays(200),
                'cantidad_inicial' => 200,
                'cantidad_actual' => 200,
                'estado' => 'Activo',
            ],
            // Amoxicilina - PorVencer (90 días)
            [
                'id_producto' => 2,
                'id_proveedor' => 1,
                'numero_lote' => 'LOT-AMOX-001',
                'fecha_fabricacion' => '2023-06-01',
                'fecha_vencimiento' => $hoy->copy()->addDays(60),
                'cantidad_inicial' => 100,
                'cantidad_actual' => 80,
                'estado' => 'PorVencer',
            ],
            // Ibuprofeno - Activo
            [
                'id_producto' => 3,
                'id_proveedor' => 2,
                'numero_lote' => 'LOT-IBU-001',
                'fecha_fabricacion' => '2024-03-10',
                'fecha_vencimiento' => $hoy->copy()->addDays(300),
                'cantidad_inicial' => 150,
                'cantidad_actual' => 150,
                'estado' => 'Activo',
            ],
            // Diazepam - Cuarentena (30 días)
            [
                'id_producto' => 4,
                'id_proveedor' => 3,
                'numero_lote' => 'LOT-DIAZ-001',
                'fecha_fabricacion' => '2022-01-01',
                'fecha_vencimiento' => $hoy->copy()->addDays(15),
                'cantidad_inicial' => 50,
                'cantidad_actual' => 40,
                'estado' => 'Cuarentena',
            ],
            // Vitamina C - Activo
            [
                'id_producto' => 5,
                'id_proveedor' => 2,
                'numero_lote' => 'LOT-VITC-001',
                'fecha_fabricacion' => '2024-02-20',
                'fecha_vencimiento' => $hoy->copy()->addDays(400),
                'cantidad_inicial' => 120,
                'cantidad_actual' => 120,
                'estado' => 'Activo',
            ],
        ]);
    }
}