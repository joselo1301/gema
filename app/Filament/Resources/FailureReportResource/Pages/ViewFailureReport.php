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
                ->form(FailureReport::getForm()),
            // CommentsAction::make(),

            // Botón adicional: Reportar
            Actions\Action::make('reportar')
                ->label('Reportar')
                ->icon('heroicon-m-paper-airplane')
                ->color('info')
                ->requiresConfirmation()
                // Muestra el botón solo si aún no fue reportado (ajusta tu condición)
                ->visible(fn () => blank($this->record->reportado_en))
                ->action(function () {
                    $reportedId = ReportFollowup::idByClave(ReportFollowup::ESTADO_REPORTADO);

                    // Actualiza el registro
                    $this->record->update([

                        'numero_reporte' => app(FailureReportNumberService::class)
                            ->makeNumberFor(
                                (int) $this->record->location_id,
                                isset($this->record->created_at) ? Carbon::parse($this->record->created_at) : now()
                            ),
                        'report_followup_id' => $reportedId,           // ⚠️ evita magic numbers: ideal usar constantes/tabla catálogos
                        'reportado_por_id'   => Auth::id(),
                        'reportado_en'       => now(),
                    ]);

                    // Notifica
                    Notification::make()
                        ->title('Reporte de falla enviado correctamente.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

                // Botón adicional: Aprobar
            Actions\Action::make('aprobar')
                ->label('Aprobar')
                ->icon('heroicon-m-document-check')
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
