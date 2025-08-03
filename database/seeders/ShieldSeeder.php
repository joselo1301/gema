<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Genera permisos y políticas para TODOS tus Resources/Pages/Widgets
        Artisan::call('shield:generate', [
            '--all'           => true,
            '--panel'         => 'gema',          // tu panel ID
            '--no-interaction'=> true,
        ]);
        $this->command->info('✔️ shield:generate ejecutado');

        // 2) Asigna super-admin al usuario #1 en el panel "gema"
        Artisan::call('shield:super-admin', [
            '--user'          => 1,
            '--panel'         => 'gema',          // idem que arriba
            '--no-interaction'=> true,
        ]);
        $this->command->info('✔️ shield:super-admin ejecutado');
    }
}
