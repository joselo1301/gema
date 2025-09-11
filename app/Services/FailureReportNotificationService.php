<?php

namespace App\Services;

use App\Mail\FailureReportMail;
use App\Models\FailureReport;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FailureReportNotificationService
{
    /**
     * Envía notificación cuando se crea un reporte de falla
     */
    public function notifyReportCreated(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('creado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }

    /**
     * Envía notificación cuando se actualiza un reporte de falla
     */
    public function notifyReportUpdated(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('actualizado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }

    /**
     * Envía notificación cuando cambia el estado de un reporte
     */
    public function notifyStatusChanged(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('estado_cambiado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }

    public function notifyReportRejected(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('rechazado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }
    
    /**
     * Envía notificación cuando se aprueba un reporte
     */
    public function notifyReportApproved(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('aprobado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }

    /**
     * Envía notificación cuando se reporta un reporte
     */
    public function notifyReportReported(
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification('reportado', $reporte, $toRoles, $ccRoles, $actor, $extra);
    }

    /**
     * Método principal para enviar notificaciones
     */
    private function sendNotification(
        string $evento, 
        FailureReport $reporte, 
        array $toRoles, 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        try {
            // Validar que se especificaron roles
            if (empty($toRoles) && empty($ccRoles)) {
                Log::warning("No se especificaron roles para notificación del reporte ID: {$reporte->id}");
                return;
            }

            // Obtener destinatarios principales (TO)
            $to = !empty($toRoles) ? $this->getRecipientsByRoles($reporte, $toRoles) : [];
            
            // Obtener destinatarios en copia (CC)
            $cc = !empty($ccRoles) ? $this->getRecipientsByRoles($reporte, $ccRoles) : [];

            // Eliminar duplicados: si un email está en TO, no debe estar en CC
            $to = array_unique($to);
            $cc = array_unique(array_diff($cc, $to));

            // Validar que hay destinatarios
            if (empty($to) && empty($cc)) {
                Log::warning("No se encontraron destinatarios para el reporte ID: {$reporte->id} con los roles especificados");
                return;
            }

            // Configurar el mail - asegurar que $to no esté vacío para Mail::to()
            if (!empty($to)) {
                $mail = Mail::to($to);
                
                if (!empty($cc)) {
                    $mail->cc($cc);
                }
            } else {
                // Si no hay destinatarios TO, usar el primer CC como TO
                $mail = Mail::to(array_shift($cc));
                
                if (!empty($cc)) {
                    $mail->cc($cc);
                }
            }

            // Enviar el correo
            $mail->queue(new FailureReportMail(
                evento: $evento,
                reporte: $reporte,
                actor: $actor,
                extra: $extra
            ));

            Log::info("Notificación enviada para reporte ID: {$reporte->id}, evento: {$evento}", [
                'to_roles' => $toRoles,
                'cc_roles' => $ccRoles
            ]);

        } catch (\Exception $e) {
            Log::error("Error enviando notificación para reporte ID: {$reporte->id}", [
                'evento' => $evento,
                'to_roles' => $toRoles,
                'cc_roles' => $ccRoles,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Obtiene destinatarios personalizados basados en roles y ubicación
     */
    public function getRecipientsByRoles(FailureReport $reporte, array $roles): array
    {
        if (empty($roles)) {
            return [];
        }

        return User::whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })
            ->whereHas('locations', function ($query) use ($reporte) {
                $query->where('locations.id', $reporte->location_id);
            })
            ->distinct()
            ->pluck('email')
            ->filter() // Eliminar emails vacíos o null
            ->unique() // Eliminar duplicados
            ->values() // Reindexar el array
            ->toArray();
            
        
    }

    /**
     * Envía notificación personalizada con roles específicos
     * Este método es un alias para mantener compatibilidad
     */
    public function notifyCustomRoles(
        string $evento, 
        FailureReport $reporte, 
        array $toRoles = [], 
        array $ccRoles = [], 
        ?User $actor = null, 
        array $extra = []
    ): void {
        $this->sendNotification($evento, $reporte, $toRoles, $ccRoles, $actor, $extra);
    }
}
