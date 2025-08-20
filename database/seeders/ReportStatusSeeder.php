<?php

namespace Database\Seeders;

use App\Models\ReportStatus;
use Illuminate\Database\Seeder;

class ReportStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReportStatus::insert([
            ['orden' => 1,  'nombre' => 'Ingresado',    'clave' => 'ingresado',     'color' => '#fe938c', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 2,  'nombre' => 'Reportado',    'clave' => 'reportado',     'color' => '#fe938c', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 3,  'nombre' => 'Notificado',   'clave' => 'notificado',    'color' => '#e6b89c', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 4,  'nombre' => 'Pendiente',    'clave' => 'pendiente',     'color' => '#ead2ac', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 5,  'nombre' => 'En proceso',   'clave' => 'en_proceso',    'color' => '#9cafb7', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 6,  'nombre' => 'Atendido',     'clave' => 'atendido',      'color' => '#4281a4', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 7,  'nombre' => 'Cerrado',      'clave' => 'cerrado',       'color' => '#60d394', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
