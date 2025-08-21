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

            ['orden' => 1,  'nombre' => 'Ingresado',            'clave' => 'ingresado',        'color' => '#F94144', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 2,  'nombre' => 'Reportado',            'clave' => 'reportado',        'color' => '#F94144', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 3,  'nombre' => 'Notificado',           'clave' => 'notificado',       'color' => '#F94144', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 4,  'nombre' => 'Por Revisar',          'clave' => 'por_revisar',      'color' => '#F94144', 'activo' => true, 'created_at' => now(), 'updated_at' => now()], // rojo
            ['orden' => 5,  'nombre' => 'Revisión',             'clave' => 'revision',         'color' => '#F3722C', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 6,  'nombre' => 'Gabinete',             'clave' => 'gabinete',         'color' => '#F8961E', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 7,  'nombre' => 'CT / SOLPED',          'clave' => 'ct_solped',        'color' => '#F9844A', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 8,  'nombre' => 'Contratación',         'clave' => 'contratacion',     'color' => '#F9C74F', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 9,  'nombre' => 'En Planificación',     'clave' => 'planificacion',    'color' => '#90BE6D', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 10, 'nombre' => 'A Programar',          'clave' => 'a_programar',      'color' => '#43AA8B', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 11, 'nombre' => 'Programado',           'clave' => 'programado',       'color' => '#4D908E', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 12, 'nombre' => 'En Ejecución',         'clave' => 'en_ejecucion',     'color' => '#577590', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 13, 'nombre' => 'Ejecutado',            'clave' => 'ejecutado',        'color' => '#277DA1', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['orden' => 14, 'nombre' => 'Observado',            'clave' => 'observado',        'color' => '#003049', 'activo' => true, 'created_at' => now(), 'updated_at' => now()], // gris
            ['orden' => 15, 'nombre' => 'No Corresponde',       'clave' => 'no_corresponde',   'color' => '#003049', 'activo' => true, 'created_at' => now(), 'updated_at' => now()], // gris claro


        ]);
    }
}
