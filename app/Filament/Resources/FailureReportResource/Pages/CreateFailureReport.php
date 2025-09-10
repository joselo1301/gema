<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use App\Models\Asset;
use App\Services\FailureReportNotificationService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateFailureReport extends CreateRecord
{
    protected static string $resource = FailureReportResource::class;

    protected static bool $canCreateAnother = false;
    
    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Guardar reporte')                      
            ->icon('heroicon-s-archive-box-arrow-down');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $asset = Asset::find($data['asset_id']);
        $locationId = $asset?->location->id;
               
        $data['location_id'] = $locationId;
        $data['report_status_id'] = 1; // Pendiente
        $data['report_followup_id'] = 1; // Ingresado
        $data['creado_por_id'] = Auth::id(); // asignar el usuario actual
        $data['actualizado_por_id'] = Auth::id(); // asignar el usuario actual
        return $data;

       
    }

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

    
}
