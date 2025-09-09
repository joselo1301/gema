<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Filament\Resources\AssetResource\Forms\AssetForm;
use App\Models\Asset;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Pages\ContentTabPosition;
use Parallax\FilamentComments\Actions\CommentsAction;


class ViewAsset extends ViewRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form(AssetForm::getForm()),
            CommentsAction::make(),
        ];
    }

    
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true; // Combina pestañas de relaciones con el contenido
    }

    public function getContentTabLabel(): ?string
    {
        return 'Activo'; // Renombra la pestaña del contenido
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-rectangle-stack'; // Icono opcional
    }

    public function getContentTabPosition(): ?ContentTabPosition
    {
        return ContentTabPosition::Before; // o ContentTabPosition::After
    }

    
}
