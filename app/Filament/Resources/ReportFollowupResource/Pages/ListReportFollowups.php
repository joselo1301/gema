<?php

namespace App\Filament\Resources\ReportFollowupResource\Pages;

use App\Filament\Resources\ReportFollowupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportFollowups extends ListRecords
{
    protected static string $resource = ReportFollowupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
