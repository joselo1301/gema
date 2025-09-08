<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Esta es una prueba de email desde GEMA', function ($message) {
        $message->to('test@example.com')
                ->subject('Prueba de Email - GEMA');
    });
    
    echo "✅ Email enviado correctamente\n";
} catch (Exception $e) {
    echo "❌ Error al enviar email: " . $e->getMessage() . "\n";
}
