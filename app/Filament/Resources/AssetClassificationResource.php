<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetClassificationResource\Pages;
use App\Filament\Resources\AssetClassificationResource\RelationManagers;
use App\Models\AssetClassification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetClassificationResource extends Resource
{
    protected static ?string $model = AssetClassification::class;

     // 1. Texto que aparece en el menú y breadcrumb
    protected static ?string $navigationLabel = 'Clasificaciones';
    // 2. Texto singular para el modelo (usado en botones, formularios, etc.)
    protected static ?string $modelLabel = 'Clasificación';
    // 3. Texto plural para el modelo (usado en título de List, breadcrumb, etc.)
    protected static ?string $pluralModelLabel = 'Clasificaciones';
    // 4. (Opcional) Ícono y posición en el menú
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    // protected static ?int $navigationSort = 2;
    // 5. Grupo de navegación
    protected static ?string $navigationGroup = 'Activos';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('orden')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('activo')
                    ->helperText('Al desactivar esta opción, no será posible registrar nuevos elementos; sin embargo, los registros existentes permanecerán disponibles para su visualización.')
                    ->default(true)
                    ->required(),
                Forms\Components\ColorPicker::make('color')
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')
                    ->default('#3B82F6'), // Default color set to white                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAssetClassifications::route('/'),
            'create' => Pages\CreateAssetClassification::route('/create'),
            'edit' => Pages\EditAssetClassification::route('/{record}/edit'),
        ];
    }
}
