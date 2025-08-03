<?php

namespace App\Filament\Resources\SystemsCatalogResource\Pages;

use App\Filament\Resources\SystemsCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSystemsCatalog extends EditRecord
{
    protected static string $resource = SystemsCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
