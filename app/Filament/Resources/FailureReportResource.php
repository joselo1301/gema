<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailureReportResource\Pages;
use App\Filament\Resources\FailureReportResource\RelationManagers;
use App\Models\FailureReport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class FailureReportResource extends Resource
{
    protected static ?string $model = FailureReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Reportes de falla';
    protected static ?string $modelLabel = 'Reporte de falla';
    protected static ?string $pluralModelLabel = 'Reportes de falla';


    public static function form(Form $form): Form
    {
        return $form

            ->columns(1)
            ->schema(FailureReport::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_reporte')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_ocurrencia')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion_corta')
                    ->searchable(),
                Tables\Columns\IconColumn::make('afecta_operaciones')
                    ->boolean(),
                Tables\Columns\IconColumn::make('afecta_medio_ambiente')
                    ->boolean(),
                Tables\Columns\TextColumn::make('asset.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assetParent.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assetState.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportStatus.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportFollowup.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reportado_en')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aprobadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('aprobado_en')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ejecutadoPor.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actualizadoPor.name')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListFailureReports::route('/'),
            'create' => Pages\CreateFailureReport::route('/create'),
            'edit' => Pages\EditFailureReport::route('/{record}/edit'),
        ];
    }
}
