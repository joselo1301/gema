<?php

namespace Database\Seeders;

use App\Models\ReportFollowup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportFollowupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       ReportFollowup::insert([

        
            ['orden' => 1,  'nombre' => 'Ingresado',        'clave' => 'ingresado',      'color' => '#ADB5BD', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 2,  'nombre' => 'Reportado',        'clave' => 'reportado',      'color' => '#8D939E', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 3,  'nombre' => 'Notificado',       'clave' => 'notificado',     'color' => '#71b39d', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

            ['orden' => 4,  'nombre' => 'Por Revisar',      'clave' => 'por_revisar',    'color' => '#FCE83A', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 5,  'nombre' => 'Revisión',         'clave' => 'revision',       'color' => '#FFD84D', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 6,  'nombre' => 'Gabinete',         'clave' => 'gabinete',       'color' => '#FFE066', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

            ['orden' => 7,  'nombre' => 'CT / SOLPED',      'clave' => 'ct_solped',      'color' => '#FFB302', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 8,  'nombre' => 'Contratación',     'clave' => 'contratacion',   'color' => '#FF9800', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 9,  'nombre' => 'En Planificación', 'clave' => 'planificacion',  'color' => '#FF8C42', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 10, 'nombre' => 'A Programar',      'clave' => 'a_programar',    'color' => '#FF7043', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

            ['orden' => 11, 'nombre' => 'Programado',       'clave' => 'programado',     'color' => '#2DCCFF', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 12, 'nombre' => 'En Ejecución',     'clave' => 'en_ejecucion',   'color' => '#1DA2F2', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

            ['orden' => 13, 'nombre' => 'Ejecutado',        'clave' => 'ejecutado',      'color' => '#198754', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

            ['orden' => 14, 'nombre' => 'Observado',        'clave' => 'observado',      'color' => '#FF3838', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 15, 'nombre' => 'No Corresponde',   'clave' => 'no_corresponde', 'color' => '#C82333', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        

        ]);
    }
}
