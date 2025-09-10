# Migración del Servicio de Notificaciones - Roles Obligatorios

## 🔄 Cambios Realizados

### ❌ **Antes (Roles por defecto)**
```php
// El servicio asumía roles por defecto
$service->notifyReportCreated($reporte, Auth::user());
$service->notifyReportUpdated($reporte, Auth::user());
$service->notifyReportApproved($reporte, Auth::user());
```

### ✅ **Ahora (Roles explícitos)**
```php
// El servicio requiere roles explícitos
$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

$service->notifyReportUpdated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

$service->notifyReportApproved(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento', 'Gerente de Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);
```

## 📋 **Beneficios del Cambio**

### 1. **Explícito vs Implícito**
- ❌ **Antes**: Roles hardcodeados en el servicio
- ✅ **Ahora**: Roles especificados en cada llamada

### 2. **Flexibilidad**
- ❌ **Antes**: Siempre los mismos roles
- ✅ **Ahora**: Diferentes roles según el contexto

### 3. **Mantenibilidad**
- ❌ **Antes**: Cambiar roles requería modificar el servicio
- ✅ **Ahora**: Cambiar roles solo requiere modificar la llamada

### 4. **Legibilidad**
- ❌ **Antes**: No se sabía qué roles se usaban sin ver el servicio
- ✅ **Ahora**: Los roles están explícitos en cada uso

## 🔧 **Ejemplos de Uso por Contexto**

### **Creación de Reporte** (Notificación Básica)
```php
$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);
```

### **Aprobación de Reporte** (Notificación Escalada)
```php
$service->notifyReportApproved(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento', 'Gerente de Mantenimiento'],
    ccRoles: ['Mecanico', 'Coordinador de Mantenimiento'],
    actor: Auth::user()
);
```

### **Reporte Crítico** (Notificación de Alta Prioridad)
```php
$service->notifyCustomRoles(
    evento: 'falla_critica',
    reporte: $reporte,
    toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta', 'Gerente General'],
    ccRoles: ['Supervisor Mantenimiento', 'Coordinador de Seguridad'],
    actor: Auth::user(),
    extra: ['nivel_criticidad' => 'ALTA']
);
```

### **Recordatorio Automático** (Notificación Programada)
```php
$service->notifyCustomRoles(
    evento: 'recordatorio_pendiente',
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: [], // Sin copia para recordatorios
    actor: null, // Sistema automático
    extra: ['dias_pendiente' => 3]
);
```

## 🏗️ **Implementación en Diferentes Contextos**

### **En Páginas de Filament**
```php
// CreateFailureReport.php
protected function afterCreate(): void
{
    $service = new FailureReportNotificationService();
    $service->notifyReportCreated(
        reporte: $this->record,
        toRoles: ['Supervisor Mantenimiento'],
        ccRoles: ['Mecanico'],
        actor: Auth::user()
    );
}

// ViewFailureReport.php - Acción personalizada
Action::make('escalar_urgente')
    ->action(function () {
        $service = new FailureReportNotificationService();
        $service->notifyCustomRoles(
            evento: 'escalacion_urgente',
            reporte: $this->record,
            toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta'],
            ccRoles: ['Supervisor Mantenimiento'],
            actor: Auth::user(),
            extra: ['motivo' => 'Falla crítica que afecta producción']
        );
    });
```

### **En Controladores API**
```php
class FailureReportController extends Controller
{
    public function approve(FailureReport $report, ApprovalRequest $request)
    {
        $report->approve($request->validated());
        
        $service = new FailureReportNotificationService();
        
        // Notificación diferente según el tipo de falla
        if ($report->is_critical) {
            $service->notifyReportApproved(
                reporte: $report,
                toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta'],
                ccRoles: ['Supervisor Mantenimiento', 'Coordinador de Seguridad'],
                actor: Auth::user()
            );
        } else {
            $service->notifyReportApproved(
                reporte: $report,
                toRoles: ['Supervisor Mantenimiento'],
                ccRoles: ['Mecanico'],
                actor: Auth::user()
            );
        }
        
        return response()->json(['message' => 'Reporte aprobado']);
    }
}
```

### **En Jobs/Comandos**
```php
class ProcessPendingReportsJob implements ShouldQueue
{
    public function handle()
    {
        $service = new FailureReportNotificationService();
        
        $criticalReports = FailureReport::critical()->pending()->get();
        
        foreach ($criticalReports as $report) {
            $service->notifyCustomRoles(
                evento: 'reporte_critico_pendiente',
                reporte: $report,
                toRoles: ['Gerente de Mantenimiento', 'Jefe de Planta'],
                ccRoles: ['Supervisor Mantenimiento'],
                actor: null, // Job del sistema
                extra: [
                    'horas_pendiente' => $report->hours_pending,
                    'impacto' => 'ALTO'
                ]
            );
        }
    }
}
```

## 🎯 **Casos de Uso Avanzados**

### **Notificaciones Condicionales**
```php
// Basado en la ubicación
$roles = $reporte->location->is_critical 
    ? ['Gerente de Mantenimiento', 'Jefe de Planta']
    : ['Supervisor Mantenimiento'];

$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: $roles,
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Basado en el turno
$roles = now()->hour >= 18 || now()->hour < 6
    ? ['Supervisor Turno Noche', 'Guardia de Seguridad']
    : ['Supervisor Mantenimiento'];

$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: $roles,
    ccRoles: [],
    actor: Auth::user()
);
```

### **Notificaciones en Cascada**
```php
// Primer nivel - Supervisores
$service->notifyReportCreated(
    reporte: $reporte,
    toRoles: ['Supervisor Mantenimiento'],
    ccRoles: ['Mecanico'],
    actor: Auth::user()
);

// Si no hay respuesta en 2 horas, escalar
if ($reporte->shouldEscalate()) {
    $service->notifyCustomRoles(
        evento: 'escalacion_automatica',
        reporte: $reporte,
        toRoles: ['Gerente de Mantenimiento'],
        ccRoles: ['Supervisor Mantenimiento'],
        actor: null,
        extra: ['motivo_escalacion' => 'Sin respuesta en 2 horas']
    );
}
```

## ✅ **Validaciones del Servicio**

El servicio incluye validaciones automáticas:

1. **Roles vacíos**: Si no se especifican roles, se registra un warning
2. **Sin destinatarios**: Si no se encuentran usuarios con los roles especificados
3. **Emails vacíos**: Filtra automáticamente emails nulos o vacíos
4. **Manejo de errores**: Captura y registra errores sin interrumpir el flujo

## 📊 **Logging Mejorado**

```
[2025-09-09 10:30:15] INFO: Notificación enviada para reporte ID: 123, evento: creado 
                           {"to_roles":["Supervisor Mantenimiento"],"cc_roles":["Mecanico"]}

[2025-09-09 10:35:22] WARNING: No se especificaron roles para notificación del reporte ID: 124

[2025-09-09 10:40:33] WARNING: No se encontraron destinatarios para el reporte ID: 125 
                                con los roles especificados

[2025-09-09 10:45:44] ERROR: Error enviando notificación para reporte ID: 126 
                             {"evento":"aprobado","to_roles":["Gerente"],"error":"Connection refused"}
```

Este enfoque hace el código más mantenible, flexible y explícito. 🚀
