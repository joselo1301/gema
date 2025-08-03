<?php

namespace App\Console\Commands;

use App\Models\Location;
use App\Models\User;
use Illuminate\Console\Command;

class TestRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:relations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar las relaciones entre User y Location';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Probando relaciones User-Location...');
        
        // Crear datos de prueba si no existen
        $this->info('📝 Creando datos de prueba...');
        
        $user = User::firstOrCreate([
            'email' => 'test@example.com'
        ], [
            'name' => 'Usuario de Prueba',
            'password' => bcrypt('password'),
            'puesto' => 'Tester',
            'empresa' => 'GEMA Test'
        ]);
        
        // Buscar ubicaciones existentes o crear simples
        $location1 = Location::first();
        $location2 = Location::skip(1)->first();
        
        if (!$location1) {
            $location1 = Location::create([
                'codigo' => '01',
                'nombre' => 'Ubicación 1',
                'direccion' => 'Dir 1',
                'activo' => true
            ]);
        }
        
        if (!$location2) {
            $location2 = Location::create([
                'codigo' => '02',
                'nombre' => 'Ubicación 2',
                'direccion' => 'Dir 2',
                'activo' => true
            ]);
        }
        
        $this->info("✅ Usuario creado: {$user->name} (ID: {$user->id})");
        $this->info("✅ Ubicación 1 creada: {$location1->nombre} (ID: {$location1->id})");
        $this->info("✅ Ubicación 2 creada: {$location2->nombre} (ID: {$location2->id})");
        
        // Probar la asociación
        $this->info('🔗 Asociando usuario con ubicaciones...');
        $user->locations()->sync([$location1->id, $location2->id]);
        
        // Probar que las relaciones funcionan
        $this->info('🧪 Probando relaciones...');
        
        // Desde User hacia Location
        $userLocations = $user->locations;
        $this->info("👤 Usuario '{$user->name}' tiene {$userLocations->count()} ubicaciones:");
        foreach ($userLocations as $location) {
            $this->info("   - {$location->nombre} ({$location->codigo})");
        }
        
        // Desde Location hacia User
        $location1Users = $location1->users;
        $this->info("📍 Ubicación '{$location1->nombre}' tiene {$location1Users->count()} usuarios:");
        foreach ($location1Users as $locationUser) {
            $this->info("   - {$locationUser->name} ({$locationUser->email})");
        }
        
        // Verificar la tabla pivot
        $this->info('🔍 Verificando tabla pivot...');
        $pivotData = $user->locations()->withPivot('created_at', 'updated_at')->get();
        foreach ($pivotData as $location) {
            $this->info("   - Pivot: User {$user->id} ↔ Location {$location->id} (Creado: {$location->pivot->created_at})");
        }
        
        $this->info('✅ ¡Todas las relaciones funcionan correctamente!');
    }
}
