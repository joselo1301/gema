<?php

namespace Database\Seeders;

use App\Models\AssetCriticality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetCriticalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssetCriticality::insert([
            [
                'nombre' => 'Esencial',
                'descripcion' => 'Máquinas o equipos que deben estar funcionando y en línea para continuar todos los procesos. La pérdida de la maquinaria afectaría considerablemente la productividad y las ganancias. Se incluyen las máquinas con altos costos de reparación o de repuestos difíciles de conseguir. Su falla puede generar altos riesgos de seguridad.',
                'nivel' => 1,
                'color' => '#B91C1C', // Default color set to white
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Crítico',
                'descripcion' => 'Equipos que limitarían una línea importante de producción. También incluye aquellos con altos costos iniciales o con problemas crónicos de mantenimiento.',
                'nivel' => 2,
                'color' => '#D97706', // Default color set to white
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Importante',
                'descripcion' => 'No críticos para la producción total, pero deben vigilarse para mantener el rendimiento aceptable.',
                'nivel' => 3,
                'color' => '#FBBF24', // Default color set to white
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Uso General',
                'descripcion' => 'Equipos exigidos que sufren desgaste prematuro pero no son críticos para el proceso productivo.',
                'nivel' => 4,
                'color' => '#3B82F6', // Default color set to white
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Auxiliares',
                'descripcion' => 'Equipos complementarios o de respaldo que no afectan directamente el proceso principal.',
                'nivel' => 5,
                'color' => '#10B981', // Default color set to white
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
