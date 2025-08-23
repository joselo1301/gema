# Política de Seguridad para Assets por Locación

## Resumen
Se ha implementado una política de seguridad que restringe el acceso a los assets basándose en las locaciones asignadas a cada usuario. Esto evita que los usuarios puedan acceder directamente a assets mediante URLs si no tienen permisos sobre la locación correspondiente.

## Cambios Realizados

### 1. Modelo User (app/Models/User.php)
Se agregaron métodos auxiliares para gestionar el acceso basado en locaciones:

```php
/**
 * Verificar si el usuario tiene acceso a un asset basado en su locación
 */
public function canAccessAsset(Asset $asset): bool
{
    return $this->locations()->where('location_id', $asset->location_id)->exists();
}

/**
 * Obtener los IDs de las locaciones asignadas al usuario
 */
public function getLocationIds(): array
{
    return $this->locations->pluck('id')->toArray();
}
```

### 2. Política de Asset (app/Policies/AssetPolicy.php)
Se modificaron los siguientes métodos para incluir verificación por locación:

- `viewAny()`: Verifica que el usuario tenga al menos una locación asignada
- `view()`: Verifica que el asset pertenezca a una locación del usuario
- `create()`: Verifica que el usuario tenga locaciones asignadas
- `update()`: Verifica acceso a la locación del asset
- `delete()`: Verifica acceso a la locación del asset
- `forceDelete()`: Verifica acceso a la locación del asset
- `restore()`: Verifica acceso a la locación del asset
- `replicate()`: Verifica acceso a la locación del asset

### 3. Recursos Filament (app/Filament/Resources/AssetResource.php)
Ya existía el filtro en `getEloquentQuery()` que limita la consulta por locaciones del usuario:

```php
public static function getEloquentQuery(): Builder
{
    $user = Filament::auth()->user();
    return parent::getEloquentQuery()
        ->whereIn('location_id', $user->locations->pluck('id'));
}
```

## Funcionamiento

### Protección a Nivel de Lista
- En la vista de lista de assets, solo se mostrarán los assets de las locaciones asignadas al usuario
- El método `getEloquentQuery()` se encarga de filtrar automáticamente

### Protección a Nivel Individual
- Si un usuario intenta acceder directamente a un asset mediante URL (ejemplo: `/admin/assets/123`)
- La política `view()` se ejecutará antes de mostrar el asset
- Si el asset no pertenece a una locación del usuario, se denegará el acceso
- Laravel mostrará una página de error 403 (Forbidden)

### Protección en Operaciones
- Todas las operaciones (crear, editar, eliminar, etc.) verifican las locaciones
- Un usuario no puede modificar assets de locaciones que no tiene asignadas
- Aunque conozca el ID del asset, la política lo protegerá

## Beneficios de Seguridad

1. **Prevención de Acceso Directo**: Los usuarios no pueden acceder a assets mediante URLs directas si no tienen permisos
2. **Consistencia**: La misma lógica se aplica tanto en listas como en vistas individuales
3. **Operaciones Protegidas**: Todas las operaciones CRUD respetan las restricciones de locación
4. **Doble Capa**: Combinación de filtros de consulta y políticas para máxima seguridad

## Testing
Se ha creado un test de feature (`AssetPolicyTest.php`) que verifica:
- Usuarios pueden acceder a assets de sus locaciones asignadas
- Usuarios no pueden acceder a assets de otras locaciones
- Usuarios con múltiples locaciones tienen acceso correcto
- Usuarios sin locaciones no tienen acceso a ningún asset

## Uso en el Código

Para verificar acceso programáticamente:
```php
// Verificar si un usuario puede acceder a un asset específico
if ($user->canAccessAsset($asset)) {
    // El usuario tiene acceso
}

// Obtener las locaciones del usuario
$locationIds = $user->getLocationIds();
```

Para usar en Filament Resources:
```php
// En cualquier Resource, el filtro se aplica automáticamente
public static function getEloquentQuery(): Builder
{
    $user = Filament::auth()->user();
    return parent::getEloquentQuery()
        ->whereIn('location_id', $user->locations->pluck('id'));
}
```

## Consideraciones Adicionales

1. Los super administradores pueden requerir acceso completo - esto se puede manejar agregando condiciones en la política
2. Si se crean nuevas relaciones con Asset, asegurar que también respeten las restricciones de locación
3. Las APIs (si existen) deberían usar las mismas políticas para mantener consistencia

## Aplicación Automática
Las políticas se aplican automáticamente en Laravel siguiendo la convención de nombres:
- `AssetPolicy` se aplica al modelo `Asset`
- No se requiere registro manual en el `AuthServiceProvider`
- Filament respeta automáticamente las políticas de Laravel
