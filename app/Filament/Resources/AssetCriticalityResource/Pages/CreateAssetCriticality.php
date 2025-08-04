<?php

namespace App\Filament\Resources\AssetCriticalityResource\Pages;

use App\Filament\Resources\AssetCriticalityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetCriticality extends CreateRecord
{
    protected static string $resource = AssetCriticalityResource::class;

     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
