<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditFailureReport extends EditRecord
{
    protected static string $resource = FailureReportResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['actualizado_por_id'] = Auth::id();
        return $data;
        
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationMessage(): ?string
    {
        return 'Reporte de falla N° ' . $this->record->numero_reporte . ' actualizado correctamente.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
