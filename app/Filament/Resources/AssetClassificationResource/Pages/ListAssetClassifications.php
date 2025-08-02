<?php

namespace App\Filament\Resources\AssetClassificationResource\Pages;

use App\Filament\Resources\AssetClassificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetClassifications extends ListRecords
{
    protected static string $resource = AssetClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
