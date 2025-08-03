<?php

namespace App\Filament\Resources\SystemsCatalogResource\Pages;

use App\Filament\Resources\SystemsCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemsCatalogs extends ListRecords
{
    protected static string $resource = SystemsCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
