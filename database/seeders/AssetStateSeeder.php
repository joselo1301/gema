<?php

namespace Database\Seeders;

use App\Models\AssetState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         AssetState::insert([
            [
                'codigo' => 'OPER',
                'nombre' => 'Operativo',
                'orden' => 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'OPE./RESTRI',
                'nombre' => 'Operativo con Restricciones',
                'orden' => 2,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'INOP.',
                'nombre' => 'Inoperativo',
                'orden' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
