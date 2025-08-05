<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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

        $data['creado_por_id'] = Auth::id(); // asignar el usuario actual
        $data['actualizado_por_id'] = Auth::id(); // asignar el usuario actual
        return $data;
    }
}
