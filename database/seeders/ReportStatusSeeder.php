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
            ['orden' => 1,  'nombre' => 'Pendiente',    'clave' => 'pendiente',     'color' => 'danger', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 2,  'nombre' => 'En proceso',   'clave' => 'en_proceso',    'color' => 'info', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 3,  'nombre' => 'Cerrado',      'clave' => 'cerrado',       'color' => 'success', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
