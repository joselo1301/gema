<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use App\Filament\Resources\FailureReportResource\Forms\FailureReportForm;
use App\Models\FailureReport;
use App\Models\ReportFollowup;
use App\Services\FailureReportNumberService;
use App\Services\FailureReportNotificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Parallax\FilamentComments\Actions\CommentsAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Rmsramos\Activitylog\ActivitylogPlugin;


class ViewFailureReport extends ViewRecord
{
    protected static string $resource = FailureReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form(FailureReportForm::getForm())
                ->icon('heroicon-m-pencil-square')
                ->visible(fn () => blank($this->record->reportado_en)),
            

            // Botón adicional: Reportar
            Action::make('reportar')
                ->authorize(fn () => Auth::user()->can('reportar', $this->record))
                ->label('Reportar')
                ->icon('heroicon-m-paper-airplane')
                ->color('info')
                ->visible(fn () => blank($this->record->reportado_en))
                ->requiresConfirmation()
                ->modalHeading('Confirmar envío del reporte de falla')
                ->modalDescription('Antes de enviar, puedes dejar un comentario para el registro.')
                ->form([
                    Textarea::make('comentario')
                        ->label('Comentario')
                        ->placeholder('Ej.: Se validó la información y se procede a reportar.')
                        ->rows(4)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_REPORTADO);

                    $reporte = $this->record;

                    // Actualiza el modelo usando Eloquent y registra actividad si es necesario
                    $reporte->report_followup_id = $reportedId;
                    $reporte->reportado_por_id = Auth::id();
                    $reporte->reportado_en = now();
                    $reporte->actualizado_por_id = Auth::id();
                    $reporte->save();

                    // Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $reporte->addSystemComment('Reporte de falla reportado: ' . $comentarioUsuario, Auth::id());
                    }

                    // Enviar notificación por email
                    $notificationService = new FailureReportNotificationService();
                    $notificationService->notifyReportReported(
                        reporte: $reporte,
                        toRoles: ['Supervisor Operativo'],
                        ccRoles: ['Supervisor Mantenimiento'],
                        actor: Auth::user()
                    );

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla enviado correctamente.')                        
                        ->success()
                        ->send();

                    

                        
                    // Redirige
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),


            Action::make('rechazar')
                ->authorize(fn () => Auth::user()->can('rechazar', $this->record))
                ->label('Rechazar')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->visible(fn () => blank($this->record->aprobado_en) && filled($this->record->reportado_en))
                ->requiresConfirmation()
                ->modalHeading('Rechazar reporte de falla')
                ->modalDescription('Antes de rechazar, dejar un comentario para el registro.')
                ->form([
                    Textarea::make('comentario')
                        ->label('Comentario')
                        ->placeholder('')
                        ->required()
                        ->rows(4)
                        ->maxLength(1000),
                ])

                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_INGRESADO);

                    // Actualiza el modelo usando Eloquent y registra actividad si es necesario
                    $this->record->report_followup_id = $reportedId;
                    $this->record->reportado_por_id = null;
                    $this->record->reportado_en = null;
                    $this->record->actualizado_por_id = Auth::id(); 
                    $this->record->save();
                    
                     // Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $this->record->addSystemComment('Reporte de falla rechazado: ' . $comentarioUsuario, Auth::id());
                    }
                    // Notificación
                    Notification::make()
                        ->title('Reporte de falla rechazado.')
                        ->success()
                        ->send();

                    // Redirige
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

                // Botón adicional: Aprobar
            Action::make('aprobar')
                ->authorize(fn () => Auth::user()->can('aprobar', $this->record))
                ->label('Aprobar')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => blank($this->record->aprobado_en) && filled($this->record->reportado_en))
                ->modalHeading('Aprobar reporte de falla')
                ->modalDescription('Antes de aprobar, puede dejar un comentario para el registro.')
                ->form([
                    Textarea::make('comentario')
                        ->label('Comentario')
                        ->placeholder('')                        
                        ->rows(4)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_NOTIFICADO);

                    // Genera el correlativo del reporte de falla
                    $numeroReporte = app(FailureReportNumberService::class)->makeNumberFor(
                        (int) $this->record->location_id,
                        isset($this->record->created_at) ? Carbon::parse($this->record->created_at) : now()
                    );

                    // Actualiza el modelo usando Eloquent y registra actividad si es necesario
                    $this->record->numero_reporte = $numeroReporte;
                    $this->record->report_followup_id = $reportedId;
                    $this->record->aprobado_por_id = Auth::id();
                    $this->record->aprobado_en = now();
                    $this->record->actualizado_por_id = Auth::id(); 
                    $this->record->save();

                    // Cambia el estado del activo y lo iguala al indicado en el reporte
                    $this->record->asset->asset_state_id = $this->record->asset_status_on_report;
                    $this->record->asset->save();

                    // Registrar log manual del cambio de estado del asset
                    activity()
                        ->useLog('Activo: Cambio de estado')
                        ->event('updated')
                        ->performedOn($this->record->asset)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'Estado de activo' => optional($this->record->assetStatusOnReport)->nombre,
                            'Numero de Reporte de falla' => $this->record->numero_reporte,
                        ])
                        ->log('Cambio de estado del activo por emisión de reporte de falla');

                    // Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $this->record->addSystemComment('Reporte de falla aprobado: ' . $comentarioUsuario, Auth::id());
                    }

                    // Servicio de notificacion por email
                    $notificationService = new FailureReportNotificationService();
                    $notificationService->notifyReportApproved(
                        reporte: $this->record,
                        toRoles: ['Supervisor Jpcm'],
                        ccRoles: [  'Supervisor Operativo',
                                    'Supervisor Mantenimiento',
                                    'Mecanico',
                                    'Coordinador Operativo'
                            ],
                        actor: Auth::user()
                        
                    );

                    // Notificación
                    Notification::make()
                        ->title('Reporte de falla aprobado correctamente.')
                        ->body("Se asignó el número de reporte {$numeroReporte}")
                        ->success()
                        ->send();

                    // Redirige a la vista del registro
                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Action::make('cambiar_etapa')
                ->authorize(fn () => Auth::user()->can('cambiarEtapa', $this->record))
                ->label('Actualizar etapa')
                ->icon('heroicon-m-forward')
                ->color('info')
                ->modalHeading('Actualizar etapa de seguimiento')
                ->modalSubmitActionLabel('Actualizar')
                ->form([
                    Select::make('report_followup_id')
                        ->label('Estado de seguimiento')
                        ->options(ReportFollowup::whereNotIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray())
                        ->required()
                        ->reactive(), // <-- Agrega esto para que el siguiente campo se actualice dinámicamente
                    Select::make('asset_status_on_close')
                        ->label('Estado del activo al cierre')
                        ->relationship('assetStatusOnClose', 'nombre', fn ($query) => $query->orderBy('orden'))
                        ->visible(fn ($get) => in_array((int)$get('report_followup_id'), [13, 14, 15]))
                        ->required(fn ($get) => in_array((int)$get('report_followup_id'), [13, 14, 15])),
                    Textarea::make('comentario')
                        ->label('Comentario')                        
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->visible(fn () => filled($this->record->aprobado_en) && blank($this->record->asset_status_on_close))
                ->action(function (array $data) {
                    $reportedId = $data['report_followup_id'];
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    $assetStatusOnClose = $data['asset_status_on_close'] ?? null;

                    // Actualiza el estado de seguimiento
                    $this->record->report_followup_id = $reportedId;
                    switch ($reportedId) {
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_EN_EJECUCION):
                            $this->record->report_status_id = 2;
                            
                            break;
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_EJECUTADO):
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_OBSERVADO):
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_NO_CORRESPONDE):
                            
                            $this->record->report_status_id = 3;
                            $this->record->asset_status_on_close = $assetStatusOnClose;
                            

                            // Cambia el estado del activo y lo iguala al indicado en el reporte
                            $this->record->asset->asset_state_id = $assetStatusOnClose;
                            $this->record->asset->save(); 
                            
                             // Registrar log manual del cambio de estado del asset
                            activity()
                                ->useLog('Activo: Cambio de estado')
                                ->event('updated')
                                ->performedOn($this->record->asset)
                                ->causedBy(Auth::user())
                                ->withProperties([
                                    'Estado de activo' => optional($this->record->assetStatusOnClose)->nombre,
                                    'R.Falla Numero' => $this->record->numero_reporte,
                                    'R.Falla Etapa' => optional($this->record->reportFollowup)->nombre,
                                    'R.Falla Estado' => optional($this->record->reportStatus)->nombre,
                                ])
                                ->log('Cambio de estado del activo por cierre de reporte de falla');

                            break;
                        
                        // Puedes agregar más casos según tus necesidades
                    }
                    $this->record->save();

                   // Agrega comentario del usuario usando el método personalizado
                    if ($comentarioUsuario !== '') {
                        $nombreEstado = ReportFollowup::find($reportedId)?->nombre ?? '';
                        $this->record->addSystemComment('Etapa actualizada "' . $nombreEstado . '": ' . $comentarioUsuario, Auth::id());
                    }

                    // Enviar notificación de cambio de estado
                    $notificationService = new FailureReportNotificationService();
                    $estadoAnterior = $this->record->getOriginal('report_followup_id');
                    $estadoNuevo = $reportedId;
                    
                    $notificationService->notifyStatusChanged(
                        reporte: $this->record,
                        toRoles: ['Supervisor Mantenimiento'],
                        ccRoles: ['Mecanico'],
                        actor: Auth::user(),
                        extra: [
                            'estado_anterior' => ReportFollowup::find($estadoAnterior)?->nombre ?? '',
                            'estado_nuevo' => ReportFollowup::find($estadoNuevo)?->nombre ?? '',
                            'comentario' => $comentarioUsuario
                        ]
                    );

                    Notification::make()
                        ->title('Etapa de seguimiento actualizada correctamente.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

            

            CommentsAction::make(),


        ];

        
    }
    
    
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true; // Si es false, las pestañas se muestran separadas
    }

    // Personaliza la etiqueta de la pestaña de contenido principal
    public function getContentTabLabel(): ?string
    {
        return 'Reporte de Falla'; // Renombra la pestaña del contenido
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-rectangle-stack'; // Icono opcional
    }
   
}
