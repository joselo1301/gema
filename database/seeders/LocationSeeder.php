<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         Location::insert([
            ['codigo' => 'IL',    'nombre' => 'Terminal Ilo',     'direccion' => 'Zona Industrial, Ilo, Moquegua',    'activo' => true, 'created_at' => now(),'updated_at' => now()],
            ['codigo' => 'MO',    'nombre' => 'Terminal Mollendo','direccion' => 'Puerto Bravo, Mollendo, Arequipa',  'activo' => false, 'created_at' => now(),'updated_at' => now()],
            ['codigo' => 'JU',    'nombre' => 'Planta Cusco',     'direccion' => 'Sector Industrial, Cusco',          'activo' => true, 'created_at' => now(),'updated_at' => now()],
            ['codigo' => 'CU',    'nombre' => 'Planta Juliaca',   'direccion' => 'Av. Aeropuerto, Juliaca, Puno',     'activo' => true, 'created_at' => now(),'updated_at' => now()],
        ]);

    }
}
