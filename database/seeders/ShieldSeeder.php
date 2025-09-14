<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        // // 1) Genera permisos y políticas para TODOS tus Resources/Pages/Widgets
        // Artisan::call('shield:generate', [
        //     '--all'           => true,
        //     '--panel'         => 'gema',          // tu panel ID
        //     '--no-interaction'=> true,
        // ]);
        // $this->command->info('✔️ shield:generate ejecutado');

        // 2) Asigna super-admin al usuario #1 en el panel "gema"
        Artisan::call('shield:super-admin', [
            '--user'          => 1,
            '--panel'         => 'gema',          // idem que arriba
            '--no-interaction'=> true,
        ]);
        $this->command->info('✔️ shield:super-admin ejecutado');

        
        $roles = [
            'CreadorRF' => [
                'view_any_failure::report',
                'view_failure::report',
                'create_failure::report',
                'update_failure::report',
                'delete_failure::report',
                'cambiar_etapa_failure::report',
                'view_any_asset',
                'view_asset'
            ],
            'ReportanteRF' => [
                'view_any_failure::report',
                'view_failure::report',
                'create_failure::report',
                'update_failure::report',
                'delete_failure::report',
                'reportar_failure::report',
                'cambiar_etapa_failure::report',
                'view_any_asset',
                'view_asset'
            ],
            'AprobadorRF' => [
                'view_any_failure::report',
                'view_failure::report',
                'aprobar_failure::report',
                'rechazar_failure::report',
                'cambiar_etapa_failure::report',
                'view_any_asset',
                'view_asset'
         ],
            'ObservadorRF' => [
                'view_any_failure::report',
                'view_failure::report',
                'view_any_asset',
                'view_asset'
            ],
            'GestorRF' => [
                'view_any_failure::report',
                'view_failure::report',
                'cambiar_etapa_failure::report',
                'view_any_asset',
                'view_asset'
            ],
        ];

        foreach ($roles as $rol => $permisos) {
            // Crear rol
            $role = Role::firstOrCreate(
                ['name' => $rol, 'guard_name' => 'web']
            );

            // Crear permisos si no existen
            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(
                    ['name' => $permiso, 'guard_name' => 'web']
                );
            }

            // Asignar permisos al rol (si tiene lista)
            if (! empty($permisos)) {
                $role->syncPermissions($permisos);
            }
        }
        
    }
}
