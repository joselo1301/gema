<?php

namespace App\Filament\Resources\ReportStatusResource\Pages;

use App\Filament\Resources\ReportStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReportStatuses extends ListRecords
{
    protected static string $resource = ReportStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
