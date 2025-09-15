<?php

namespace App\Filament\Resources\AssetResource\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;
use Filament\Facades\Filament;

class AssetTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->paginationPageOptions([9, 25, 50, 100])
            ->columns([

                Split::make([
                ImageColumn::make('foto')
                    ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('assets', 'thumb') ?: null)
                    ->circular() // o ->square()
                    ->height(80)
                    ->width(80)
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($record->nombre) . '&size=80';
                    }),

                    Stack::make([
                        
                        
                        Tables\Columns\TextColumn::make('nombre')
                            ->searchable()
                            ->weight(FontWeight::Bold),
                    ]),
                ]),
                
                Split::make([
                    Tables\Columns\TextColumn::make('tag')
                        ->searchable()
                        ->icon('heroicon-m-tag'),
                    
                   
                ]),

                Split::make([
                    Stack::make([
                    Tables\Columns\TextColumn::make('assetClassification.nombre')
                        ->label('Clasificación')
                        ->tooltip(function ($record) {
                            return $record->assetClassification->descripcion ?? '';
                                
                        })
                        ->sortable()
                        ->size('xs')
                        ->html()
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetClassification->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        }),

                    Tables\Columns\TextColumn::make('assetCriticality.nombre')
                        ->label('Criticidad')
                        ->tooltip(function ($record) {
                            return $record->assetCriticality->descripcion ?? '';
                        })
                        ->sortable()
                        ->size('xs')
                        ->html()
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetCriticality->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        }),
                    ]),

                    Tables\Columns\IconColumn::make('activo')
                        ->label('Activo')
                        ->alignment('right')
                        ->boolean()                        
                        ->tooltip(function ($state) {
                            return $state ? 'Activo' : 'Inactivo';
                        }),
                    

                ]),
                
                 Split::make([
                

                    Tables\Columns\TextColumn::make('assetState.nombre')
                    ->badge()
                    ->label('Estado')
                    ->sortable()
                    ->color(fn ($record) => $record->assetState->color ?? 'gray')
                    ->formatStateUsing(fn ($state) => $state ? "Estado {$state}" : '-')
                    ->size('sm'),

                    Tables\Columns\TextColumn::make('location.nombre')
                            ->tooltip('Centro')
                            ->alignment('right')
                            ->sortable(),

                ]), 
                
                Stack::make([
                    Tables\Columns\TextColumn::make('assetParent.nombre')
                        
                        ->alignCenter()
                        ->sortable()
                        ->formatStateUsing(fn ($state) => $state ? "Activo superior: {$state}" : '-')
                        ->tooltip(fn ($state) => $state ? "{$state}" : null)
                        ->size('xs')
                        ->weight(FontWeight::Bold)
                        ->extraAttributes(fn ($state) => [
                            'class' => $state
                                ? 'border border-gray-300 rounded-md px-2 py-1'
                                : '',
                        ]),
                        
                ]),
                    
            ])
            ->filters([
                // Filtro por Ubicación
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Planta o Terminal')
                    ->relationship('location', 'nombre', function ($query) {
                        $user = Filament::auth()->user();
                        return $query->whereIn('id', $user->locations->pluck('id'))->orderBy('nombre');
                    })
                ->visible(fn () => Filament::auth()->user()->locations->count() > 1),
                // Filtro por Sistema
                Tables\Filters\SelectFilter::make('systems_catalog_id')
                    ->label('Sistema')
                    ->relationship('systemsCatalog', 'nombre'),
                // Filtro por Clasificación
                Tables\Filters\SelectFilter::make('asset_classification_id')
                    ->label('Clasificación')
                    ->relationship('assetClassification', 'nombre'),

                // Filtro por Criticidad
                Tables\Filters\SelectFilter::make('asset_criticality_id')
                    ->label('Criticidad')
                    ->relationship('assetCriticality', 'nombre'),

                // Filtro por Estado
                Tables\Filters\SelectFilter::make('asset_state_id')
                    ->label('Estado')
                    ->relationship('assetState', 'nombre'),
                        ])
            ->actions([

                ActivityLogTimelineTableAction::make('Historial')
                    ->withRelations(['profile', 'address'])
                    ->icon('heroicon-s-eye')
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'info',
                        'restored' => 'warning',                       
                    ])
                    ->limit(10),

                CommentsAction::make(),

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
}
