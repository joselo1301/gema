<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('codigo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tag')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('descripcion')
                    ->maxLength(255),
                Forms\Components\TextInput::make('modelo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fabricante')
                    ->maxLength(255),
                Forms\Components\TextInput::make('serie')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ubicacion')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('fecha_adquisicion'),
                Forms\Components\DatePicker::make('fecha_puesta_marcha'),
                Forms\Components\TextInput::make('foto')
                    ->maxLength(255),
                Forms\Components\Toggle::make('activo')
                    ->required(),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'id')
                    ->required(),
                Forms\Components\Select::make('systems_catalog_id')
                    ->relationship('systemsCatalog', 'id')
                    ->required(),
                Forms\Components\Select::make('asset_classification_id')
                    ->relationship('assetClassification', 'id')
                    ->required(),
                Forms\Components\Select::make('asset_criticality_id')
                    ->relationship('assetCriticality', 'id')
                    ->required(),
                Forms\Components\Select::make('asset_state_id')
                    ->relationship('assetState', 'id')
                    ->required(),
                Forms\Components\Select::make('asset_parent_id')
                    ->relationship('assetParent', 'id'),
                Forms\Components\Select::make('creado_por_id')
                    ->relationship('creadoPor', 'name')
                    ->required(),
                Forms\Components\Select::make('actualizado_por_id')
                    ->relationship('actualizadoPor', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginationPageOptions([9, 25, 50, 100])
            ->columns([
              
                Tables\Columns\TextColumn::make('location.nombre')
                    ->label('Centro')
                    ->alignment('right')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('assetState.nombre')
                    ->label('Estado')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? "Estado {$state}" : '-')
                    ->color(fn ($record) => $record->assetState->color ?? 'gray'),

                Stack::make([
                    Tables\Columns\TextColumn::make('tag')
                        ->searchable()
                        ->icon('heroicon-m-tag'),
                    
                    Tables\Columns\TextColumn::make('codigo')
                        ->searchable(),
                    
                    Tables\Columns\TextColumn::make('assetClassification.nombre')
                        ->label('Clasificación')
                        ->sortable()
                        ->alignment('right')
                        ->size('xs')
                        ->html()
                        ->wrap(false)
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetClassification->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        }),

                    Tables\Columns\TextColumn::make('assetCriticality.nombre')
                        ->label('Criticidad')
                        ->sortable()
                        ->alignment('right')
                        ->size('xs')
                        ->html()
                        ->wrap(false)
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetCriticality->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        }),

                    Tables\Columns\TextColumn::make('assetParent.nombre')
                        ->label('Activo Padre')
                        ->sortable()
                        ->formatStateUsing(fn ($state) => $state ? "Padre: {$state}" : '-'),
                    
                    Tables\Columns\IconColumn::make('activo')
                        ->alignment('right')
                        ->boolean(),
                    ])
            ])
            ->filters([
                 
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->contentGrid([
            'md' => 2,
            'xl' => 3,
            ]); 
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
