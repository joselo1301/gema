<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use App\Filament\Resources\FailureReportResource\Forms\FailureReportForm;
use App\Models\FailureReport;
use App\Models\ReportFollowup;
use App\Models\User;
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
use Filament\Infolists\Components\TextEntry;
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
                ->modalHeading('Confirmar envío del reporte de falla')
                ->modalDescription('Antes de enviar, puedes dejar un comentario para el registro.')
                ->modalSubmitActionLabel('Reportar')
                ->modalAlignment('center')
                ->modalFooterActionsAlignment('right')
                ->modalIcon('heroicon-m-paper-airplane')
                ->form([
                    Textarea::make('comentario')
                        ->label('Comentario')
                        // ->placeholder('Ej.: Se validó la información y se procede a reportar.')
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
                        toRoles: ['AprobadorRF'],
                        ccRoles: ['ReportanteRF'],
                        actor: Auth::user(),
                        extra: ['comentario' => $comentarioUsuario]
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
                ->modalHeading('Rechazar reporte de falla')
                ->modalDescription('Detalla el motivo por el cual se rechaza el reporte.')
                ->modalSubmitActionLabel('Rechazar')
                ->modalAlignment('center')
                ->modalFooterActionsAlignment('right')
                ->modalIcon('heroicon-m-x-circle')
                ->form([
                    Textarea::make('comentario')
                        ->label('Motivo')
                        ->placeholder('')
                        ->required()
                        ->rows(4)
                        ->maxLength(1000),
                    
                ])

                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_INGRESADO);

                    $reporte = $this->record;

                    $reportado_por_nombre_old = $reporte->reportadoPor->name;
                    $reportado_por_puesto_old = $reporte->reportadoPor->puesto;
                    $reportado_por_empresa_old = $reporte->reportadoPor->empresa;
                    
                    // Actualiza el modelo usando Eloquent y registra actividad si es necesario
                    $reporte->report_followup_id = $reportedId;
                    $reporte->reportado_por_id = null;
                    $reporte->reportado_en = null;
                    $reporte->actualizado_por_id = Auth::id();
                    $reporte->save();

                    // Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $reporte->addSystemComment('Reporte de falla rechazado: ' . $comentarioUsuario, Auth::id());
                    }

                    // Servicio de notificacion por email
                    $notificationService = new FailureReportNotificationService();
                    $notificationService->notifyReportRejected(
                        reporte: $reporte,
                        toRoles: ['ReportanteRF'],
                        ccRoles: ['CreadorRF'],
                        actor: Auth::user(),
                        extra: ['comentario' => $comentarioUsuario,
                            'reportado_por_nombre' => $reportado_por_nombre_old,
                            'reportado_por_puesto' => $reportado_por_puesto_old,
                            'reportado_por_empresa' => $reportado_por_empresa_old,
                        ]
                        
                    );

                    // Notificación
                    Notification::make()
                        ->title('Reporte de falla rechazado.')
                        ->success()
                        ->send();
                    
                    $this->redirect(static::getResource()::getUrl('index'));
                                      
                }),

            Action::make('aprobar')
                ->authorize(fn () => Auth::user()->can('aprobar', $this->record))
                ->label('Aprobar')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(fn () => blank($this->record->aprobado_en) && filled($this->record->reportado_en))
                ->modalHeading('Aprobar reporte de falla')
                ->modalDescription('Antes de aprobar, puede dejar un comentario para el registro.')
                ->modalSubmitActionLabel('Aprobar')
                ->modalAlignment('center')
                ->modalFooterActionsAlignment('right')
                ->modalIcon('heroicon-m-check-circle')
                ->form([
                    Textarea::make('comentario')
                        ->label('Comentario')
                        ->placeholder('')                        
                        ->rows(4)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_NOTIFICADO);

                    $reporte = $this->record;

                    // Genera el correlativo del reporte de falla
                    $numeroReporte = app(FailureReportNumberService::class)->makeNumberFor(
                        (int) $reporte->location_id,
                        isset($reporte->created_at) ? Carbon::parse($reporte->created_at) : now()
                    );

                    // Actualiza el modelo usando Eloquent y registra actividad si es necesario
                    $reporte->numero_reporte = $numeroReporte;
                    $reporte->report_followup_id = $reportedId;
                    $reporte->aprobado_por_id = Auth::id();
                    $reporte->aprobado_en = now();
                    $reporte->actualizado_por_id = Auth::id(); 
                    $reporte->save();

                    // Cambia el estado del activo y lo iguala al indicado en el reporte
                    $reporte->asset->asset_state_id = $reporte->asset_status_on_report;
                    $reporte->asset->save();

                    // Registrar log manual del cambio de estado del asset
                    activity()
                        ->useLog('Activo: Cambio de estado')
                        ->event('updated')
                        ->performedOn($reporte->asset)
                        ->causedBy(Auth::user())
                        ->withProperties([
                            'Estado de activo' => optional($reporte->assetStatusOnReport)->nombre,
                            'Numero de Reporte de falla' => $reporte->numero_reporte,
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
                        reporte: $reporte,
                        toRoles: ['GestorRF'],
                        ccRoles: ['AprobadorRF', 'ReportanteRF', 'CreadorRF', 'ObservadorRF'],
                        actor: Auth::user(),
                        extra: ['comentario' => $comentarioUsuario]
                       
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
                ->modalAlignment('center')
                ->modalFooterActionsAlignment('right')
                ->modalIcon('heroicon-m-forward')
                ->form([
                    Select::make('report_followup_id')
                        ->label('Estado de seguimiento')
                        ->options(ReportFollowup::whereNotIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray())
                        ->required()
                        ->reactive()
                        ->default($this->record->report_followup_id), // Muestra el valor actual
                    Select::make('asset_status_on_close')
                        ->label('Estado del activo al cierre')
                        ->relationship('assetStatusOnClose', 'nombre', fn ($query) => $query->orderBy('orden'))
                        ->visible(fn ($get) => in_array((int)$get('report_followup_id'), [13, 14, 15]))
                        ->required(fn ($get) => in_array((int)$get('report_followup_id'), [13, 14, 15])),
                    Textarea::make('comentario')
                        ->label('Nota')                        
                        ->rows(3)
                        ->required(),
                ])
                
                ->visible(fn () => filled($this->record->aprobado_en) && blank($this->record->asset_status_on_close))
                ->action(function (array $data) {
                    $reporte = $this->record;
                    $reportedId = $data['report_followup_id'];
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    $assetStatusOnClose = $data['asset_status_on_close'] ?? null;
                    $estadoAnterior = $reporte->report_followup_id;

                    // Actualiza el estado de seguimiento
                    $reporte->report_followup_id = $reportedId;
                    switch ($reportedId) {
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_EN_EJECUCION):
                            $reporte->report_status_id = 2;
                            
                            break;
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_EJECUTADO):
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_OBSERVADO):
                        case ReportFollowup::idByClave(ReportFollowup::ESTADO_NO_CORRESPONDE):
                            
                            $reporte->report_status_id = 3;
                            $reporte->asset_status_on_close = $assetStatusOnClose;
                            $reporte->asset->asset_state_id = $assetStatusOnClose;
                            
                            
                             // Registrar log manual del cambio de estado del asset
                            activity()
                                ->useLog('Activo: Cambio de estado')
                                ->event('updated')
                                ->performedOn($reporte->asset)
                                ->causedBy(Auth::user())
                                ->withProperties([
                                    'Estado de activo' => optional($reporte->assetStatusOnClose)->nombre,
                                    'R.Falla Numero' => $reporte->numero_reporte,
                                    'R.Falla Etapa' => optional($reporte->reportFollowup)->nombre,
                                    'R.Falla Estado' => optional($reporte->reportStatus)->nombre,
                                ])
                                ->log('Cambio de estado del activo por cierre de reporte de falla');

                            break;
                        
                        // Puedes agregar más casos según tus necesidades
                    }
                    $reporte->actualizado_por_id = Auth::id();
                    $reporte->save();

                   // Agrega comentario del usuario usando el método personalizado
                    if ($comentarioUsuario !== '') {
                        $nombreEstado = ReportFollowup::find($reportedId)?->nombre ?? '';
                        $reporte->addSystemComment('Etapa actualizada "' . $nombreEstado . '": ' . $comentarioUsuario, Auth::id());
                    }

                    // Servicio de notificacion por email
                    $notificationService = new FailureReportNotificationService();
                    
                    $estadoNuevo = $reportedId;
                    
                    $notificationService->notifyStatusChanged(
                        reporte: $reporte,
                        toRoles: ['ReportanteRF','GestorRF'],
                        ccRoles: ['AprobadorRF','CreadorRF','ObservadorRF'],
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

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $reporte]));
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
