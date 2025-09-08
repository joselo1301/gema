<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

try {
    $users = User::with('roles')->get(['id', 'name', 'email']);
    
    echo "=== TODOS LOS USUARIOS Y SUS ROLES ===\n";
    foreach ($users as $user) {
        echo "Usuario: {$user->name} ({$user->email})\n";
        if ($user->roles->count() > 0) {
            foreach ($user->roles as $role) {
                echo "  - Rol: {$role->name}\n";
            }
        } else {
            echo "  - Sin roles asignados\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
