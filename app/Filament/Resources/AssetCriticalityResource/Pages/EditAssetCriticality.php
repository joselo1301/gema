<?php

namespace App\Filament\Resources\AssetCriticalityResource\Pages;

use App\Filament\Resources\AssetCriticalityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetCriticality extends EditRecord
{
    protected static string $resource = AssetCriticalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
