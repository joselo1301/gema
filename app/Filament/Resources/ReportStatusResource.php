<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportStatusResource\Pages;
use App\Filament\Resources\ReportStatusResource\RelationManagers;
use App\Models\ReportStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportStatusResource extends Resource
{
    protected static ?string $model = ReportStatus::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $navigationLabel = 'Estados';
    protected static ?string $modelLabel = 'Estado';
    protected static ?string $pluralModelLabel = 'Estados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('clave')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('color')
                    ->maxLength(255),
                Forms\Components\TextInput::make('orden')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('activo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clave')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orden')
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
            'index' => Pages\ListReportStatuses::route('/'),
            'create' => Pages\CreateReportStatus::route('/create'),
            'edit' => Pages\EditReportStatus::route('/{record}/edit'),
        ];
    }
}
