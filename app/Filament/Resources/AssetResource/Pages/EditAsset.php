<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\ContentTabPosition;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['asset_parent_id'])) {
            $parent = Asset::find($data['asset_parent_id']);

            if ($parent) {
                $data['location_id'] = $parent->location_id;
                $data['systems_catalog_id'] = $parent->systems_catalog_id;
                $data['asset_classification_id'] = $parent->asset_classification_id;
                $data['asset_criticality_id'] = $parent->asset_criticality_id;
            }
        }

        $data['actualizado_por_id'] = Auth::id(); // asignar el usuario actual
        return $data;
    }

    
    
}
