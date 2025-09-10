# Servicio de Notificaciones para Reportes de Fallas

## FailureReportNotificationService

Este servicio centraliza toda la lógica de envío de notificaciones por correo electrónico para los reportes de fallas, siguiendo el principio DRY (Don't Repeat Yourself) y facilitando el mantenimiento del código.

## Características

- ✅ **Reutilizable**: Se puede usar en cualquier parte del sistema
- ✅ **Configurable**: Permite personalizar destinatarios por roles
- ✅ **Logging**: Registra automáticamente los envíos y errores
- ✅ **Validación**: Verifica que existan destinatarios antes de enviar
- ✅ **Manejo de errores**: Captura y registra errores sin interrumpir el flujo

## Métodos Principales

### 1. Notificaciones con Roles Obligatorios

Todos los métodos del servicio requieren que se especifiquen explícitamente los roles de destinatarios:

```php
use App\Services\FailureReportNotificationService;

$service = new FailureReportNotificationService();

// Cuando se crea un reporte - ROLES OBLIGATORIOS
$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Cuando se actualiza un reporte
$service->notifyReportUpdated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Cuando se aprueba un reporte
$service->notifyReportApproved(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento', 'Gerente de Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Cuando se reporta un reporte
$service->notifyReportReported(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Cuando cambia el estado
$service->notifyStatusChanged(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user(),
    extra: [
        'estado_anterior' => 'Ingresado',
        'estado_nuevo' => 'En Ejecución',
        'comentario' => 'Iniciando trabajos de mantenimiento'
    ]
);
```

### 2. Notificaciones Personalizadas

```php
// Enviar a roles específicos (método alternativo)
$service->notifyCustomRoles(
    evento: 'mantenimiento_programado',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento', 'Jefe de Planta'],
    ccRoles: ['Mecanico', 'Electricista'],
    actor: Auth::user(),
    extra: ['fecha_programada' => '2025-09-15']
);
```

## Configuración de Destinatarios

### ⚠️ Roles Obligatorios
**El servicio NO tiene roles predefinidos**. Siempre debes especificar explícitamente:
- `toRoles`: Array de roles para destinatarios principales (TO)
- `ccRoles`: Array de roles para destinatarios en copia (CC) - opcional

### Ejemplos de Roles Comunes
- `'Supervisor Mantenimiento'`
- `'Mecanico'`
- `'Gerente de Mantenimiento'`
- `'Jefe de Planta'`
- `'Coordinador de Mantenimiento'`
- `'Electricista'`

### Obtener Destinatarios por Roles

```php
// Obtener destinatarios por roles específicos
$destinatarios = $service->getRecipientsByRoles($reporte, [
    'Supervisor Mantenimiento',
    'Jefe de Turno',
    'Coordinador de Mantenimiento'
]);
```

## Ejemplos de Uso en Different Contextos

### 1. En Páginas de Filament

#### CreateFailureReport.php
```php
protected function afterCreate(): void
{
    $notificationService = new FailureReportNotificationService();
    $notificationService->notifyReportCreated(
        reporte: $this->record,
        toRoles: ['Supervisor Mantenimiento'],
        ccRoles: ['Mecanico'],
        actor: Auth::user()
    );
}
```

#### EditFailureReport.php
```php
protected function afterSave(): void
{
    $notificationService = new FailureReportNotificationService();
    $notificationService->notifyReportUpdated(
        reporte: $this->record,
        toRoles: ['Supervisor Mantenimiento'],
        ccRoles: ['Mecanico'],
        actor: Auth::user()
    );
}
```

#### ViewFailureReport.php - Acción Personalizada
```php
Action::make('enviar_recordatorio')
    ->action(function () {
        $service = new FailureReportNotificationService();
        $service->notifyCustomRoles(
            evento: 'recordatorio',
            reporte: $this->record,
            toRoles: ['Supervisor Mantenimiento'],
            ccRoles: ['Mecanico'],
            actor: Auth::user(),
            extra: ['tipo_recordatorio' => 'seguimiento_pendiente']
        );
        
        Notification::make()
            ->title('Recordatorio enviado correctamente')
            ->success()
            ->send();
    })
```

### 2. En Controladores

```php
class FailureReportController extends Controller
{
    public function escalateReport(FailureReport $report)
    {
        $service = new FailureReportNotificationService();
        
        // Escalar a roles superiores
        $service->notifyCustomRoles(
            evento: 'escalado',
            reporte: $report,
            toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta'],
            ccRoles: ['Supervisor Mantenimiento'],
            actor: Auth::user(),
            extra: ['motivo_escalacion' => 'Reporte crítico sin respuesta']
        );
        
        return response()->json(['message' => 'Reporte escalado correctamente']);
    }
}
```

### 3. En Comandos de Artisan

```php
class SendPendingReportsCommand extends Command
{
    public function handle()
    {
        $service = new FailureReportNotificationService();
        
        $reportesPendientes = FailureReport::where('report_status_id', 1)
            ->where('created_at', '<', now()->subDays(2))
            ->get();
            
        foreach ($reportesPendientes as $reporte) {
            $service->notifyCustomRoles(
                evento: 'recordatorio_pendiente',
                reporte: $reporte,
                toRoles: ['Supervisor Mantenimiento'],
                actor: null,
                extra: ['dias_pendiente' => now()->diffInDays($reporte->created_at)]
            );
        }
    }
}
```

### 4. En Jobs/Colas

```php
class ProcessFailureReportJob implements ShouldQueue
{
    public function handle()
    {
        $service = new FailureReportNotificationService();
        
        // Procesar y notificar
        $service->notifyReportApproved($this->report, $this->user);
    }
}
```

### 5. En Eventos del Sistema

```php
class FailureReportEventListener
{
    public function handle($event)
    {
        $service = new FailureReportNotificationService();
        
        switch($event->type) {
            case 'critical_failure':
                $service->notifyCustomRoles(
                    evento: 'falla_critica',
                    reporte: $event->report,
                    toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta', 'Gerente General'],
                    ccRoles: ['Supervisor Mantenimiento', 'Coordinador de Seguridad'],
                    extra: ['nivel_criticidad' => 'ALTA']
                );
                break;
        }
    }
}
```

## Logging y Monitoreo

El servicio automáticamente registra:

- ✅ Envíos exitosos con información del reporte y evento
- ❌ Errores de envío con detalles del problema
- ⚠️ Advertencias cuando no se encuentran destinatarios

```php
// Los logs se registran en storage/logs/laravel.log
[2025-09-09 10:30:15] local.INFO: Notificación enviada para reporte ID: 123, evento: creado
[2025-09-09 10:35:22] local.WARNING: No se encontraron destinatarios para el reporte ID: 124
[2025-09-09 10:40:33] local.ERROR: Error enviando notificación para reporte ID: 125 {"evento":"aprobado","error":"Connection refused"}
```

## Ventajas del Servicio

1. **Centralización**: Toda la lógica de notificaciones en un solo lugar
2. **Reutilización**: Usar en cualquier parte del sistema sin duplicar código
3. **Mantenibilidad**: Cambios centralizados afectan todo el sistema
4. **Flexibilidad**: Fácil personalización de destinatarios y mensajes
5. **Debugging**: Logging centralizado facilita la resolución de problemas
6. **Escalabilidad**: Fácil agregar nuevos tipos de notificaciones

## Próximas Mejoras

- [ ] Configuración de destinatarios desde base de datos
- [ ] Templates de correo personalizables por evento
- [ ] Integración con otros canales (SMS, Slack, Teams)
- [ ] Dashboard de monitoreo de notificaciones
- [ ] Rate limiting para evitar spam
- [ ] Notificaciones condicionales basadas en reglas de negocio
