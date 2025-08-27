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

class ViewFailureReport extends ViewRecord
{
    protected static string $resource = FailureReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form(FailureReport::getForm())
                ->visible(fn () => blank($this->record->reportado_en)),
            // CommentsAction::make(),

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
                    ]);

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla rechazado correctamente.')
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
        ];

        
    }

   
}
