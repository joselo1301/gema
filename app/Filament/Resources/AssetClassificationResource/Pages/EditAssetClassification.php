<?php

namespace App\Filament\Resources\AssetClassificationResource\Pages;

use App\Filament\Resources\AssetClassificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetClassification extends EditRecord
{
    protected static string $resource = AssetClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
