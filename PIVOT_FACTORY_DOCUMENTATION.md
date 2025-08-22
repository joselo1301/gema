# Factories para Tablas Pivot

Este documento explica cómo usar los factories para tablas pivot en tu aplicación Laravel.

## Tablas Pivot Existentes

### 1. `location_users` (User ↔ Location)
- **Factory**: `LocationUserFactory`
- **Relación**: Many-to-Many entre User y Location
- **Campos**: `user_id`, `location_id`, `created_at`, `updated_at`

### 2. `failure_report_people` (FailureReport ↔ People)
- **Factory**: `FailureReportPeopleFactory`
- **Relación**: Many-to-Many entre FailureReport y People
- **Campos**: `failure_report_id`, `people_id`, `created_at`, `updated_at`

## Formas de Usar los Factories

### Método 1: Usar el Factory Directamente (No recomendado para producción)

```php
use Database\Factories\LocationUserFactory;
use Illuminate\Support\Facades\DB;

// Crear instancia del factory
$factory = new LocationUserFactory();

// Generar datos
$data = $factory->definition();

// Insertar en la tabla pivot
DB::table('location_users')->insert($data);
```

### Método 2: Usando Relaciones Eloquent (Recomendado)

```php
use App\Models\User;
use App\Models\Location;

// Crear usuarios y ubicaciones
$user = User::factory()->create();
$locations = Location::factory(3)->create();

// Asociar usando relaciones
$user->locations()->attach($locations->pluck('id')->toArray());

// O usar sync para reemplazar asociaciones existentes
$user->locations()->sync($locations->pluck('id')->toArray());

// O usar syncWithoutDetaching para agregar sin eliminar existentes
$user->locations()->syncWithoutDetaching($locations->pluck('id')->toArray());
```

### Método 3: En Seeders

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LocationSeeder::class,
            PeopleSeeder::class,
            FailureReportSeeder::class,
            PivotTablesSeeder::class, // Llamar al final
        ]);
    }
}
```

## Ejemplos Prácticos

### Asociar un Usuario con Múltiples Ubicaciones

```php
$user = User::factory()->create();
$locations = Location::factory(3)->create();

// Método 1: Usando attach
$user->locations()->attach($locations);

// Método 2: Usando sync
$user->locations()->sync($locations->pluck('id'));

// Método 3: Con timestamps personalizados
$user->locations()->attach([
    $locations[0]->id => ['created_at' => now(), 'updated_at' => now()],
    $locations[1]->id => ['created_at' => now(), 'updated_at' => now()],
]);
```

### Asociar un FailureReport con Personas

```php
$failureReport = FailureReport::factory()->create();
$people = People::factory(2)->create();

// Asociar personas que detectaron el fallo
$failureReport->detectadoPor()->attach($people);

// Verificar la asociación
foreach ($failureReport->detectadoPor as $person) {
    echo "Detectado por: {$person->nombres} {$person->apellidos}\n";
}
```

### En Tests

```php
public function test_user_can_have_multiple_locations()
{
    $user = User::factory()->create();
    $locations = Location::factory(2)->create();
    
    $user->locations()->attach($locations);
    
    $this->assertCount(2, $user->locations);
    $this->assertTrue($user->locations->contains($locations[0]));
}
```

## Comandos Artisan Útiles

```bash
# Crear un factory
php artisan make:factory LocationUserFactory

# Crear un seeder
php artisan make:seeder PivotTablesSeeder

# Ejecutar seeders
php artisan db:seed --class=PivotTablesSeeder

# Ejecutar todos los seeders
php artisan db:seed
```

## Notas Importantes

1. **No necesitas modelos** para tablas pivot simples. Laravel las maneja automáticamente.
2. **Usa `withTimestamps()`** en las relaciones si tu tabla pivot tiene campos `created_at` y `updated_at`.
3. **Prefiere usar relaciones Eloquent** en lugar de insertar directamente en la tabla pivot.
4. **En tests**, las relaciones pivot se pueden probar fácilmente con los métodos de colección.

## Estructura de Archivos

```
database/
├── factories/
│   ├── LocationUserFactory.php          ✅ Creado
│   └── FailureReportPeopleFactory.php    ✅ Creado
└── seeders/
    └── PivotTablesSeeder.php             ✅ Creado
```
