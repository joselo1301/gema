<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use App\Models\FailureReport;
use App\Models\ReportFollowup;
use App\Services\FailureReportNumberService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Parallax\FilamentComments\Actions\CommentsAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;


class ViewFailureReport extends ViewRecord
{
    protected static string $resource = FailureReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form(FailureReport::getForm())
                ->icon('heroicon-m-pencil-square')
                ->visible(fn () => blank($this->record->reportado_en)),
            

            // Botón adicional: Reportar
            Action::make('reportar')
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

                    // 1) Actualiza FailureReport
                    $numeroReporte = app(FailureReportNumberService::class)->makeNumberFor(
                        (int) $this->record->location_id,
                        isset($this->record->created_at) ? Carbon::parse($this->record->created_at) : now()
                    );

                    $this->record->update([
                        'numero_reporte' => $numeroReporte,
                        'report_followup_id' => $reportedId,
                        'reportado_por_id'   => Auth::id(),
                        'reportado_en'       => now(),
                    ]);

                    

                    // 3) Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $this->record->addSystemComment('Reporte de falla reportado: ' . $comentarioUsuario, Auth::id());
                    }

                    // 4) Agrega comentario automático del sistema
                    // $this->record->addSystemComment(
                    //     "Reporte #{$numeroReporte} enviado automáticamente por el sistema. Usuario: " . Auth::user()->name,
                    //     Auth::id()
                    // );

                    // 5) Notifica y redirige
                    \Filament\Notifications\Notification::make()
                        ->title('Reporte de falla enviado correctamente.')
                        ->body("Se generó el reporte #{$numeroReporte}")
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),


            Action::make('rechazar')
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

                    // Actualiza el registro
                    $this->record->update([

                        'report_followup_id' => $reportedId,      
                        'reportado_por_id'   => null,
                        'reportado_en'       => null,
                    ]);

                    // 2) Actualiza estado del Asset (si aplica)
                    if ($this->record->asset) {
                        $this->record->asset->update([
                            'asset_state_id' => $this->record->asset_status_on_report,
                        ]);
                    }

                     // 3) Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $this->record->addSystemComment('Reporte de falla rechazado: ' . $comentarioUsuario, Auth::id());
                    }
                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla rechazado.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

                // Botón adicional: Aprobar
            Actions\Action::make('aprobar')
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
                        ->required()
                        ->rows(4)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_NOTIFICADO);

                    // Actualiza el registro
                    $this->record->update([

                        'report_followup_id' => $reportedId,
                        'aprobado_por_id'   => Auth::id(),
                        'aprobado_en'       => now(),
                    ]);

                    // 3) Agrega comentario del usuario usando el método personalizado
                    $comentarioUsuario = trim($data['comentario'] ?? '');
                    if ($comentarioUsuario !== '') {
                        $this->record->addSystemComment('Reporte de falla aprobado: ' . $comentarioUsuario, Auth::id());
                    }

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla aprobado correctamente.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

                Actions\Action::make('cambiar_etapa')
                    ->label('Actualizar etapa')
                    ->icon('heroicon-m-forward')
                    ->color('info')
                    ->modalHeading('Actualizar etapa de seguimiento')
                    ->modalSubmitActionLabel('Actualizar')
                    ->form([
                        Select::make('report_followup_id')
                            ->label('Estado de seguimiento')
                            ->options(ReportFollowup::whereNotIn('id', [1, 2, 3])->pluck('nombre', 'id')->toArray())
                            ->required(),
                        Textarea::make('comentario')
                            ->label('Comentario')
                            ->required()
                            ->rows(3),
                    ])
                    ->requiresConfirmation()
                    ->visible(fn () => filled($this->record->aprobado_en))
                    ->action(function (array $data) {
                        // Actualiza el estado de seguimiento
                        $this->record->update([
                            'report_followup_id' => $data['report_followup_id'],
                        ]);

                       // 3) Agrega comentario del usuario usando el método personalizado
                        $comentarioUsuario = trim($data['comentario'] ?? '');
                        if ($comentarioUsuario !== '') {
                            $this->record->addSystemComment('Etapa actualizada "' . ReportFollowup::find($data['report_followup_id'])->nombre . '": ' . $comentarioUsuario, Auth::id());
                        }

                        Notification::make()
                            ->title('Etapa de seguimiento actualizada correctamente.')
                            ->success()
                            ->send();

                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    }),

                    CommentsAction::make(),
        ];

        
    }

   
}
