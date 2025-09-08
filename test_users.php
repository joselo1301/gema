<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

try {
    $supervisores = User::whereHas('roles', function ($query) {
        $query->where('name', 'Supervisor Operativo');
    })->count();
    
    $coordinadores = User::whereHas('roles', function ($query) {
        $query->where('name', 'Coordinador Operativo');
    })->count();
    
    echo "✅ Usuarios con rol 'Supervisor Operativo': {$supervisores}\n";
    echo "✅ Usuarios con rol 'Coordinador Operativo': {$coordinadores}\n";
    
    if ($supervisores === 0 && $coordinadores === 0) {
        echo "⚠️  No hay usuarios con los roles requeridos para recibir emails\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
