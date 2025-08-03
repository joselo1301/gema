<?php

namespace App\Filament\Resources\AssetCriticalityResource\Pages;

use App\Filament\Resources\AssetCriticalityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssetCriticalities extends ListRecords
{
    protected static string $resource = AssetCriticalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
