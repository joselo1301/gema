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

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $record->update($data);
        
        // Si este activo tiene hijos, propagar los cambios de herencia
        if ($record->hasChildren()) {
            $this->updateChildrenWithInheritedFields($record);
        }

        return $record;
    }

    

    /**
     * Actualiza los campos de herencia en todos los activos hijos
     */
    private function updateChildrenWithInheritedFields(Asset $record): void
    {
        $inheritableFields = [
            'location_id',
            'systems_catalog_id', 
            'asset_classification_id',
            'asset_criticality_id'
        ];

        $updateData = [];
        foreach ($inheritableFields as $field) {
            $updateData[$field] = $record->$field;
        }
        
        // Agregar el usuario que actualiza
        $updateData['actualizado_por_id'] = Auth::id();

        // Actualizar todos los hijos directos
        $record->children()->update($updateData);

        // Recursivamente actualizar los nietos y descendientes
        $this->updateDescendantsRecursively($record, $updateData);
    }

    /**
     * Actualiza recursivamente todos los descendientes
     */
    private function updateDescendantsRecursively(Asset $parent, array $updateData): void
    {
        foreach ($parent->children as $child) {
            if ($child->hasChildren()) {
                $child->children()->update($updateData);
                $this->updateDescendantsRecursively($child, $updateData);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
    
    
}
