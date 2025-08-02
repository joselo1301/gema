<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Limpia cache de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
       
        // Crear permisos
        Permission::firstOrCreate(['name' => 'crear reportes']);
        Permission::firstOrCreate(['name' => 'ver reportes']);
        Permission::firstOrCreate(['name' => 'editar reportes']);
        Permission::firstOrCreate(['name' => 'eliminar reportes']);



        // Crear roles y asignarles permisos
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $generador = Role::firstOrCreate(['name' => 'generador']);
        $generador->givePermissionTo(['crear reportes', 'ver reportes', 'editar reportes']);

        $aprobador = Role::firstOrCreate(['name' => 'aprobador']);
        $aprobador->givePermissionTo(['crear reportes', 'ver reportes', 'editar reportes']);

        $responsable = Role::firstOrCreate(['name' => 'responsable']);
        $responsable->givePermissionTo(['editar reportes']);

        $observador = Role::firstOrCreate(['name' => 'observador']);
        $observador->givePermissionTo(['ver reportes']);
    }
}
