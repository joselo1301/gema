<?php

namespace App\Filament\Resources\AssetStateResource\Pages;

use App\Filament\Resources\AssetStateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAssetState extends EditRecord
{
    protected static string $resource = AssetStateResource::class;

     protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['actualizado_por_id'] = Auth::id();
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    //Retorna al URL de listado
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
