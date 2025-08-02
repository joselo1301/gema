<?php

namespace Database\Seeders;

use App\Models\AssetClassification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssetClassification::insert([
            [
                'nombre' => 'Infraestructura',
                'descripcion' => 'Elementos estructurales o fijos que permiten el soporte general de la operación.',
                'orden' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Utilidades',
                'descripcion' => 'Sistemas de soporte como energía, aire, agua, etc. necesarios para la operación del proceso.',
                'orden' => 2,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Proceso',
                'descripcion' => 'Equipos directamente involucrados en la producción o proceso operativo.',
                'orden' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
