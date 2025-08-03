<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetCriticalityResource\Pages;
use App\Filament\Resources\AssetCriticalityResource\RelationManagers;
use App\Models\AssetCriticality;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetCriticalityResource extends Resource
{
    protected static ?string $model = AssetCriticality::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Activos';
    protected static ?string $navigationLabel = 'Criticidades';
    protected static ?string $modelLabel = 'Criticidad';
    protected static ?string $pluralModelLabel = 'Criticidades';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('nivel')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nivel')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListAssetCriticalities::route('/'),
            'create' => Pages\CreateAssetCriticality::route('/create'),
            'edit' => Pages\EditAssetCriticality::route('/{record}/edit'),
        ];
    }
}
