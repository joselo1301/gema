<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Genera los permisos de FilamentShield en la tabla `permissions`
        // Artisan::call('shield:generate');

        // 2. Crea o recupera el rol super-admin
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // 3. Asigna **todos** los permisos (incluidos los de Shield)
        $role->syncPermissions(Permission::all());

        // 4. Crea o recupera el usuario
        $user = User::firstOrCreate(
            ['email' => 'admin@gema.test'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('PasswordSeguro123'),
            ]
        );

        // 5. Asigna el rol super-admin
        $user->assignRole($role);
    }
}
