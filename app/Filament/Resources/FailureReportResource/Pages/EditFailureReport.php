<?php

namespace App\Filament\Resources\FailureReportResource\Pages;

use App\Filament\Resources\FailureReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFailureReport extends EditRecord
{
    protected static string $resource = FailureReportResource::class;

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
