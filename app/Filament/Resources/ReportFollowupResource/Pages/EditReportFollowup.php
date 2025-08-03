<?php

namespace App\Filament\Resources\ReportFollowupResource\Pages;

use App\Filament\Resources\ReportFollowupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReportFollowup extends EditRecord
{
    protected static string $resource = ReportFollowupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
