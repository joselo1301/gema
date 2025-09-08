<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

try {
    // Usuarios con rol Supervisor Operativo
    $supervisores = User::whereHas('roles', function ($query) {
        $query->where('name', 'Supervisor Operativo');
    })->with('roles')->get(['id', 'name', 'email']);
    
    echo "=== USUARIOS CON ROL 'Supervisor Operativo' ===\n";
    if ($supervisores->count() > 0) {
        foreach ($supervisores as $user) {
            echo "- {$user->name} ({$user->email})\n";
        }
    } else {
        echo "No hay usuarios con este rol\n";
    }
    
    // Usuarios con rol Coordinador Operativo  
    $coordinadores = User::whereHas('roles', function ($query) {
        $query->where('name', 'Coordinador Operativo');
    })->with('roles')->get(['id', 'name', 'email']);
    
    echo "\n=== USUARIOS CON ROL 'Coordinador Operativo' ===\n";
    if ($coordinadores->count() > 0) {
        foreach ($coordinadores as $user) {
            echo "- {$user->name} ({$user->email})\n";
        }
    } else {
        echo "No hay usuarios con este rol\n";
    }
    
    // Total de usuarios
    $totalUsers = User::count();
    echo "\n=== RESUMEN ===\n";
    echo "Total de usuarios en el sistema: {$totalUsers}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
