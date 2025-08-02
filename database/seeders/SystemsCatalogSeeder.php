<?php

namespace Database\Seeders;

use App\Models\SystemsCatalog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemsCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemsCatalog::insert([
            ['orden' => 1, 'codigo' => 'Tq', 'nombre' => 'Área de tanques', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 2, 'codigo' => 'Ei', 'nombre' => 'Edificios e Instalaciones', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 3, 'codigo' => 'Ec', 'nombre' => 'Equipos de Comunicaciones y Afines', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 4, 'codigo' => 'In', 'nombre' => 'Equipos de Inspección', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 5, 'codigo' => 'Ge', 'nombre' => 'Grupos Electrógenos', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 6, 'codigo' => 'Oe', 'nombre' => 'Otros Equipos', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 7, 'codigo' => 'Sc', 'nombre' => 'Sistema Contra Incendio', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 8, 'codigo' => 'Bo', 'nombre' => 'Sistema de Bombeo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 9, 'codigo' => 'De', 'nombre' => 'Sistema de Despacho y Aditivación', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 10, 'codigo' => 'Re', 'nombre' => 'Sistema de Recepción', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 11, 'codigo' => 'Va', 'nombre' => 'Sistema de vapor', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 12, 'codigo' => 'El', 'nombre' => 'Sistema Eléctrico', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 13, 'codigo' => 'Pc', 'nombre' => 'Sistemas de Protección Contra la Corrosión  y Puesta Tierra', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 14, 'codigo' => 'Pt', 'nombre' => 'Sistemas de Puestas Tierra y pararrayos', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
