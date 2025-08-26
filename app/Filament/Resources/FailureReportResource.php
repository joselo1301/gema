<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FailureReportResource\Pages;
use App\Filament\Resources\FailureReportResource\RelationManagers;
use App\Models\Asset;
use App\Models\FailureReport;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;


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
                    ->label('N° Reporte')
                    ->description(fn ($record): string => $record->asset?->nombre ?? '')
                    ->searchable(),

                Tables\Columns\TextColumn::make('asset.nombre')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_ocurrencia')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('descripcion_corta')
                    ->label('Descripción Corta')
                    ->wrap()
                    ->lineClamp(2)
                    
                    ->searchable(),

                Tables\Columns\TextColumn::make('location.nombre')
                    ->label('Ubicación')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reportStatus.nombre')
                    ->label('Estado')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->reportStatus?->color)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('reportFollowup.nombre')
                    ->label('Etapa')
                    ->alignCenter()
                    ->sortable()
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $hexColor = $record->reportFollowup->color ?? '#000000';
                        return "<span style='color: {$hexColor}'>{$state}</span>";
                    }),

               
                    
                Tables\Columns\TextColumn::make('creadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('reportadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('aprobadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('ejecutadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('actualizadoPor.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('reportStatus_id')
                    ->label('Estado de reporte')
                    ->relationship('reportStatus', 'nombre'),
                // Filtro por Sistema
                Tables\Filters\SelectFilter::make('reportFollowup_id')
                    ->label('Etapa de Reporte')
                    ->relationship('reportFollowup', 'nombre'),
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'nombre')                    
                    ->visible(fn () => Filament::auth()->user()->locations->count() > 1),
                
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'view' => Pages\ViewFailureReport::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return parent::getEloquentQuery()
            ->whereIn('location_id', $user->locations->pluck('id'));
    }

}
