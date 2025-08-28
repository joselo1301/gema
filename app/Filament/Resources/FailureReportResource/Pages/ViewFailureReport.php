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
            Actions\Action::make('reportar')
                ->label('Reportar')
                ->icon('heroicon-m-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                // Muestra el botón solo si aún no fue reportado
                ->visible(fn () => blank($this->record->reportado_en))
                ->action(function () {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_REPORTADO);

                    // Actualiza el registro de FailureReport
                    $this->record->update([
                        'numero_reporte' => app(FailureReportNumberService::class)
                            ->makeNumberFor(
                                (int) $this->record->location_id,
                                isset($this->record->created_at) ? Carbon::parse($this->record->created_at) : now()
                            ),
                        'report_followup_id' => $reportedId,
                        'reportado_por_id'   => Auth::id(),
                        'reportado_en'       => now(),
                    ]);

                    // Actualiza el estado del asset relacionado
                    if ($this->record->asset) {
                        $this->record->asset->update([
                            'asset_state_id' => $this->record->asset_status_on_report,
                        ]);
                    }

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla enviado correctamente.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('rechazar')
                ->label('Rechazar')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => blank($this->record->aprobado_en) && filled($this->record->reportado_en))
                ->action(function () {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_INGRESADO);

                    // Actualiza el registro
                    $this->record->update([

                        'report_followup_id' => $reportedId,      
                        'reportado_por_id'   => null,
                        'reportado_en'       => null,
                    ]);

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
                ->action(function () {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_NOTIFICADO);

                    // Actualiza el registro
                    $this->record->update([

                        'report_followup_id' => $reportedId,
                        'aprobado_por_id'   => Auth::id(),
                        'aprobado_en'       => now(),
                    ]);

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla notificado correctamente.')
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
                        \Filament\Forms\Components\Select::make('report_followup_id')
                            ->label('Estado de seguimiento')
                            ->options(ReportFollowup::pluck('nombre', 'id')->toArray())
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('comentario')
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

                        // Agrega el comentario usando HasFilamentComments
                        if (method_exists($this->record, 'comments')) {
                            $this->record->comments()->create([
                                'body' => $data['comentario'],
                                'commentator_id' => Auth::id(),
                            ]);
                        } elseif (method_exists($this->record, 'addComment')) {
                            $this->record->addComment([
                                'body' => $data['comentario'],
                                'commentator_id' => Auth::id(),
                            ]);
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
