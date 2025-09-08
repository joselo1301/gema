<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Mail\FailureReportMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

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
        $reporte = $this->record;

        // Destinatarios (ajusta según tu lógica)
        $to = User::whereHas('roles', function ($query) {
            $query->where('name', 'Supervisor Mantenimiento');
            })
            ->whereHas('locations', function ($query) use ($reporte) {
            $query->where('locations.id', $reporte->location_id);
            })
            ->pluck('email')
            ->all();
        $cc = User::whereHas('roles', function ($query) {
            $query->where('name', 'Mecanico');
            })
            ->whereHas('locations', function ($query) use ($reporte) {
            $query->where('locations.id', $reporte->location_id);
            })
            ->pluck('email')
            ->all();

        // Enviar mailable
        Mail::to($to)
            ->cc($cc)
            ->queue(new FailureReportMail(
                evento: 'creado',
                reporte: $reporte,
                actor: Auth::user()
            ));
    }

    //  protected function afterCreate(): void
    // {
    //     $reporte = $this->record;

    //     // Destinatarios (ajusta según tu lógica)
    //     $to = User::whereHas('roles', function ($query) {
    //         $query->where('name', 'Supervisor Operativo');
    //         })
    //         ->whereHas('locations', function ($query) use ($reporte) {
    //         $query->where('locations.id', $reporte->location_id);
    //         })
    //         ->pluck('email')
    //         ->all();
    //     $cc = User::whereHas('roles', function ($query) {
    //         $query->where('name', 'Coordinador Operativo');
    //         })
    //         ->whereHas('locations', function ($query) use ($reporte) {
    //         $query->where('locations.id', $reporte->location_id);
    //         })
    //         ->pluck('email')
    //         ->all();

    //     // Enviar mailable
    //     Mail::to($to)
    //         ->cc($cc)
    //         ->queue(new FailureReportMail(
    //             evento: 'creado',
    //             reporte: $reporte,
    //             actor: Auth::user()
    //         ));
    // }
}
