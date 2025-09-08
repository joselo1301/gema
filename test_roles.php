<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;

try {
    $roles = Role::all(['name']);
    
    echo "Roles disponibles en el sistema:\n";
    foreach ($roles as $role) {
        echo "- {$role->name}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
