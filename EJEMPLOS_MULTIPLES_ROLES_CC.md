# Ejemplos de Múltiples Roles en ccRoles

## Servicio de Notificaciones - Ejemplos con Múltiples Roles

A continuación se muestran ejemplos prácticos de cómo usar múltiples roles en el parámetro `ccRoles` del servicio `FailureReportNotificationService`.

## 1. Ejemplo Básico - Múltiples Roles en Copia

```php
use App\Services\FailureReportNotificationService;

$service = new FailureReportNotificationService();

// Enviar notificación con múltiples roles en copia
$service->notifyCustomRoles(
    evento: 'reporte_critico',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: [
        'Mecanico',
        'Electricista', 
        'Soldador',
        'Coordinador de Turno',
        'Jefe de Seguridad'
    ],
    actor: Auth::user(),
    extra: ['nivel_criticidad' => 'ALTO']
);
```

## 2. Escenarios por Tipo de Falla

### Falla Eléctrica
```php
$service->notifyCustomRoles(
    evento: 'falla_electrica',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: [
        'Electricista',
        'Jefe de Turno',
        'Coordinador de Seguridad',
        'Operador de Planta'
    ],
    actor: Auth::user()
);
```

### Falla Mecánica
```php
$service->notifyCustomRoles(
    evento: 'falla_mecanica',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: [
        'Mecanico',
        'Soldador',
        'Tornero',
        'Lubricador',
        'Almacenista'
    ],
    actor: Auth::user()
);
```

### Falla de Seguridad
```php
$service->notifyCustomRoles(
    evento: 'falla_seguridad',
    reporte: $reporte,
    toRoles: [
        'Jefe de Seguridad',
        'Gerente de Planta'
    ],
    ccRoles: [
        'Supervisor Mantenimiento',
        'Coordinador de Seguridad',
        'Medico Ocupacional',
        'Bombero Industrial',
        'Prevencionista'
    ],
    actor: Auth::user(),
    extra: ['requiere_atencion_inmediata' => true]
);
```

## 3. Escalamiento por Niveles

### Nivel 1 - Operativo
```php
$service->notifyCustomRoles(
    evento: 'escalamiento_nivel_1',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: [
        'Mecanico',
        'Electricista',
        'Operador de Turno'
    ],
    actor: Auth::user()
);
```

### Nivel 2 - Táctico
```php
$service->notifyCustomRoles(
    evento: 'escalamiento_nivel_2',
    reporte: $reporte,
    toRoles: [
        'Jefe de Mantenimiento',
        'Coordinador de Producción'
    ],
    ccRoles: [
        'Supervisor Mantenimiento',
        'Jefe de Turno',
        'Planificador de Mantenimiento',
        'Coordinador de Materiales',
        'Analista de Confiabilidad'
    ],
    actor: Auth::user(),
    extra: ['tiempo_sin_resolver' => '4 horas']
);
```

### Nivel 3 - Estratégico
```php
$service->notifyCustomRoles(
    evento: 'escalamiento_nivel_3',
    reporte: $reporte,
    toRoles: [
        'Gerente de Mantenimiento',
        'Gerente de Producción',
        'Gerente de Planta'
    ],
    ccRoles: [
        'Jefe de Mantenimiento',
        'Coordinador de Producción',
        'Jefe de Seguridad',
        'Controller de Planta',
        'Jefe de Calidad',
        'Coordinador de Proyectos'
    ],
    actor: Auth::user(),
    extra: [
        'impacto_produccion' => 'ALTO',
        'costo_estimado' => 50000
    ]
);
```

## 4. Notificaciones por Turno

### Turno Día
```php
$service->notifyCustomRoles(
    evento: 'reporte_turno_dia',
    reporte: $reporte,
    toRoles: ['Supervisor Turno Día'],
    ccRoles: [
        'Mecanico Turno Día',
        'Electricista Turno Día',
        'Operador Turno Día',
        'Lubricador Turno Día'
    ],
    actor: Auth::user()
);
```

### Turno Noche
```php
$service->notifyCustomRoles(
    evento: 'reporte_turno_noche',
    reporte: $reporte,
    toRoles: ['Supervisor Turno Noche'],
    ccRoles: [
        'Mecanico Turno Noche',
        'Electricista Turno Noche', 
        'Operador Turno Noche',
        'Guardia de Seguridad'
    ],
    actor: Auth::user()
);
```

## 5. Implementación en Acciones de Filament

### En ViewFailureReport.php
```php
// Acción personalizada con múltiples roles
Action::make('notificar_equipo_completo')
    ->label('Notificar Equipo Completo')
    ->icon('heroicon-m-user-group')
    ->color('info')
    ->action(function () {
        $service = new FailureReportNotificationService();
        
        $service->notifyCustomRoles(
            evento: 'notificacion_equipo_completo',
            reporte: $this->record,
            toRoles: [
                'Supervisor Mantenimiento',
                'Jefe de Mantenimiento'
            ],
            ccRoles: [
                'Mecanico',
                'Electricista',
                'Soldador',
                'Instrumentista',
                'Planificador',
                'Almacenista',
                'Coordinador de Seguridad'
            ],
            actor: Auth::user(),
            extra: ['motivo' => 'Notificación masiva solicitada por usuario']
        );
        
        Notification::make()
            ->title('Equipo completo notificado')
            ->success()
            ->send();
    })
```

## 6. Notificaciones Condicionales

### Basado en Criticidad del Activo
```php
public function notifyBasedOnCriticality(FailureReport $reporte, User $actor)
{
    $service = new FailureReportNotificationService();
    $criticality = $reporte->asset->criticality_level;
    
    switch($criticality) {
        case 'CRITICO':
            $service->notifyCustomRoles(
                evento: 'falla_activo_critico',
                reporte: $reporte,
                toRoles: [
                    'Gerente de Mantenimiento',
                    'Gerente de Producción'
                ],
                ccRoles: [
                    'Jefe de Mantenimiento',
                    'Supervisor Mantenimiento',
                    'Coordinador de Producción',
                    'Jefe de Turno',
                    'Planificador Senior',
                    'Analista de Confiabilidad'
                ],
                actor: $actor
            );
            break;
            
        case 'IMPORTANTE':
            $service->notifyCustomRoles(
                evento: 'falla_activo_importante',
                reporte: $reporte,
                toRoles: ['Jefe de Mantenimiento'],
                ccRoles: [
                    'Supervisor Mantenimiento',
                    'Mecanico Senior',
                    'Electricista Senior',
                    'Planificador'
                ],
                actor: $actor
            );
            break;
            
        case 'NORMAL':
            $service->notifyCustomRoles(
                evento: 'falla_activo_normal',
                reporte: $reporte,
                toRoles: ['Supervisor Mantenimiento'],
                ccRoles: [
                    'Mecanico',
                    'Electricista'
                ],
                actor: $actor
            );
            break;
    }
}
```

## 7. Notificaciones por Área/Departamento

### Área de Producción
```php
$service->notifyCustomRoles(
    evento: 'falla_area_produccion',
    reporte: $reporte,
    toRoles: ['Supervisor Producción'],
    ccRoles: [
        'Operador Línea A',
        'Operador Línea B', 
        'Coordinador de Calidad',
        'Mecanico de Producción',
        'Electricista de Producción'
    ],
    actor: Auth::user()
);
```

### Área de Utilities
```php
$service->notifyCustomRoles(
    evento: 'falla_utilities',
    reporte: $reporte,
    toRoles: ['Supervisor Utilities'],
    ccRoles: [
        'Operador de Calderas',
        'Operador de Aire Comprimido',
        'Operador de Agua de Proceso',
        'Electricista de Utilities',
        'Instrumentista'
    ],
    actor: Auth::user()
);
```

## 8. Uso en Comandos Programados

### Reporte Diario
```php
// En un comando artisan
class DailyFailureReportCommand extends Command
{
    public function handle()
    {
        $service = new FailureReportNotificationService();
        $reportesPendientes = FailureReport::whereIn('report_status_id', [1, 2])->get();
        
        foreach($reportesPendientes as $reporte) {
            $service->notifyCustomRoles(
                evento: 'reporte_diario_pendientes',
                reporte: $reporte,
                toRoles: ['Jefe de Mantenimiento'],
                ccRoles: [
                    'Supervisor Mantenimiento',
                    'Planificador de Mantenimiento',
                    'Coordinador de Materiales',
                    'Analista de Costos'
                ],
                actor: null,
                extra: [
                    'dias_pendiente' => $reporte->created_at->diffInDays(now()),
                    'tipo_reporte' => 'diario'
                ]
            );
        }
    }
}
```

## 9. Validación de Roles Existentes

Antes de usar roles en `ccRoles`, puedes validar que existan:

```php
use Spatie\Permission\Models\Role;

// Validar roles antes de notificar
$rolesDisponibles = Role::pluck('name')->toArray();
$rolesDeseados = [
    'Mecanico',
    'Electricista',
    'Soldador',
    'Coordinador de Turno'
];

$rolesValidos = array_intersect($rolesDeseados, $rolesDisponibles);

if (!empty($rolesValidos)) {
    $service->notifyCustomRoles(
        evento: 'notificacion_validada',
        reporte: $reporte,
        toRoles: ['Supervisor Mantenimiento'],
        ccRoles: $rolesValidos,
        actor: Auth::user()
    );
}
```

## 10. Tip: Configuración Dinámica

```php
// Crear configuraciones dinámicas
$configuraciones = [
    'falla_critica' => [
        'toRoles' => ['Gerente de Mantenimiento'],
        'ccRoles' => ['Supervisor Mantenimiento', 'Jefe de Seguridad', 'Coordinador de Producción']
    ],
    'mantenimiento_preventivo' => [
        'toRoles' => ['Planificador'],
        'ccRoles' => ['Mecanico', 'Electricista', 'Lubricador']
    ]
];

$config = $configuraciones['falla_critica'];
$service->notifyCustomRoles(
    evento: 'falla_critica',
    reporte: $reporte,
    toRoles: $config['toRoles'],
    ccRoles: $config['ccRoles'],
    actor: Auth::user()
);
```

Todos estos ejemplos muestran la flexibilidad del servicio para manejar múltiples roles en copia, permitiendo notificaciones granulares y específicas según el contexto del reporte de falla.
