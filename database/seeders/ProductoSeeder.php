<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('productos')->insert([
            [
                'id_categoria' => 1,
                'codigo_barras' => '7501234567890',
                'nombre' => 'Paracetamol 500mg',
                'descripcion' => 'Caja x 100 tabletas',
                'precio_venta' => 15.50,
                'es_controlado' => false,
                'requiere_receta' => false,
                'stock_actual' => 200,
                'stock_minimo' => 50,
            ],
            [
                'id_categoria' => 2,
                'codigo_barras' => '7501234567891',
                'nombre' => 'Amoxicilina 500mg',
                'descripcion' => 'Caja x 12 cápsulas',
                'precio_venta' => 35.00,
                'es_controlado' => true,
                'requiere_receta' => true,
                'stock_actual' => 80,
                'stock_minimo' => 20,
            ],
            [
                'id_categoria' => 3,
                'codigo_barras' => '7501234567892',
                'nombre' => 'Ibuprofeno 400mg',
                'descripcion' => 'Caja x 50 tabletas',
                'precio_venta' => 22.00,
                'es_controlado' => false,
                'requiere_receta' => false,
                'stock_actual' => 150,
                'stock_minimo' => 30,
            ],
            [
                'id_categoria' => 5,
                'codigo_barras' => '7501234567893',
                'nombre' => 'Diazepam 10mg',
                'descripcion' => 'Caja x 30 tabletas - CONTROLADO',
                'precio_venta' => 45.00,
                'es_controlado' => true,
                'requiere_receta' => true,
                'stock_actual' => 40,
                'stock_minimo' => 10,
            ],
            [
                'id_categoria' => 4,
                'codigo_barras' => '7501234567894',
                'nombre' => 'Vitamina C 1000mg',
                'descripcion' => 'Frasco x 60 tabletas',
                'precio_venta' => 28.00,
                'es_controlado' => false,
                'requiere_receta' => false,
                'stock_actual' => 120,
                'stock_minimo' => 25,
            ],
        ]);
    }
}