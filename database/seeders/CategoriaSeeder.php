<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categorias')->insert([
            ['nombre' => 'Analgésicos', 'descripcion' => 'Medicamentos para el dolor'],
            ['nombre' => 'Antibióticos', 'descripcion' => 'Requieren receta médica'],
            ['nombre' => 'Antigripales', 'descripcion' => 'Resfrío y gripe'],
            ['nombre' => 'Vitaminas', 'descripcion' => 'Suplementos nutricionales'],
            ['nombre' => 'Psicotrópicos', 'descripcion' => 'Medicamentos controlados'],
            ['nombre' => 'Dermatológicos', 'descripcion' => 'Cuidado de la piel'],
        ]);
    }
}