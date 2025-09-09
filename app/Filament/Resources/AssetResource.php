<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Forms\AssetForm;
use App\Filament\Resources\AssetResource\Tables\AssetTable;
use App\Filament\Resources\AssetResource\Infolists\AssetInfolist;
use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Activos';
    protected static ?string $modelLabel = 'Activo';
    protected static ?string $pluralModelLabel = 'Activos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(AssetForm::getForm());       
    }

    public static function table(Table $table): Table
    {
        return AssetTable::getTable($table);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return AssetInfolist::getInfolist($infolist);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Actividades', [
                ActivitylogRelationManager::class,
            ])
            ->icon('heroicon-m-clock'),

           
        ];  

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
            'view' => Pages\ViewAsset::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return parent::getEloquentQuery()
            ->whereIn('location_id', $user->locations->pluck('id'));
    }

}
