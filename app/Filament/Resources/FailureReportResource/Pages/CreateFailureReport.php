<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
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
        $data['numero_reporte'] = 'RF-IL-001-2025'; // asignar el usuario actual
        $data['report_status_id'] = 1; // asignar el usuario actual
        $data['report_followup_id'] = 1; // asignar el usuario actual
        $data['creado_por_id'] = Auth::id(); // asignar el usuario actual
        $data['actualizado_por_id'] = Auth::id(); // asignar el usuario actual
        return $data;

        // ('numero_reporte')
        // ('reportado_por_id')
        // ('reportado_en')
        // ('aprobado_por_id')
        // ('aprobadoPor', 'name')
        // ('aprobado_en')
        // ('ejecutado_por_id')
        // ('ejecutadoPor', 'name')
        // ('actualizadoPor', 'name')
    }
}
