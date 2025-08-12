<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{Grid, Section, Split as SplitInfo, ImageEntry, TextEntry, IconEntry, ColorEntry, Fieldset, Tabs};
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;

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

            ->schema(Asset::getForm());       
           
    }

    public static function table(Table $table): Table
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
                        ->formatStateUsing(fn ($state) => $state ? "Activo Padre: {$state}" : '-')
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
                    ->relationship('location', 'nombre'),
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
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'info',                       
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            

        ->schema([
            Grid::make()
                ->columns([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                            'xl' => 12,
                        ])
                ->schema([

                Grid::make()
                ->columns(2)
                ->schema([

                    TextEntry::make('nombre')
                        ->size('lg')
                        ->label('')
                        ->state(fn ($record) => $record->nombre),

                    TextEntry::make('locacion.nombre')
                    ->alignRight()
                    ->label('')
                    ->state(fn ($record) => $record->location->nombre ?? '-'),

                    

                    
                        
                ]),
                        
                Section::make()
                ->columnSpan(3)
                ->extraAttributes(['class' => '!gap-y-1']) 
                ->schema([
                    
                    ImageEntry::make('foto')
                        ->size(100)
                        ->alignCenter()
                        ->square()
                        ->label('')
                        ->state(fn ($record) => $record->getFirstMediaUrl('assets'))
                        ->hidden(fn ($record) => blank($record->getFirstMediaUrl('assets'))),

                    TextEntry::make('foto')
                        ->label('')
                        ->badge()
                        ->icon('heroicon-o-photo')
                        ->alignCenter()
                        ->url(fn ($record) => $record->getFirstMediaUrl('assets'))
                        ->state('Ver Imagen')
                        ->openUrlInNewTab()
                        ->hidden(fn ($record) => blank($record->getFirstMediaUrl('assets'))),

                    TextEntry::make('tag')
                        ->alignCenter()
                        ->size('xs')
                        ->label('')
                        ->state(fn ($record) => 'Tag: ' . $record->tag),
                            
                    TextEntry::make('systemsCatalog.nombre')
                        ->alignCenter()
                        ->badge()
                        ->color('info')
                        ->label('')
                        ->state(fn ($record) => $record->systemsCatalog->nombre ?? '-'),
                        
                    TextEntry::make('assetClassification.nombre')
                        ->alignCenter()
                        ->label('')
                        ->state(fn ($record) => $record->assetClassification->nombre ?? '-')
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetClassification->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        })
                        ->html(),
                                                

                    TextEntry::make('assetCriticality.nombre')
                        ->alignCenter()
                        ->label('')
                        ->state(fn ($record) => $record->assetCriticality->nombre ?? '-')
                        ->formatStateUsing(function ($state, $record) {
                            $hexColor = $record->assetCriticality->color ?? '#000000';
                            return "<span style='color: {$hexColor}'>{$state}</span>";
                        })
                        ->html(),

                    TextEntry::make('assetState.nombre')
                        ->alignCenter()
                        ->badge()
                        ->color(fn ($record) => $record->assetState->color ?? '-')
                        ->label('')
                        ->state(fn ($record) => $record->assetState->nombre ?? '-'),
                    

                    
                    
                ]),
                
            Section::make()
                ->columnSpan(9)
                ->columns(3)
                ->schema([
                    
                    TextEntry::make('fecha_adquisicion')
                        ->label('Adquisición')
                        ->state(fn ($record) => $record->fecha_adquisicion ? $record->fecha_adquisicion->translatedFormat('d, M Y'): '-'),

                    TextEntry::make('fecha_puesta_marcha')
                        ->label('Puesta en Marcha')
                        ->state(fn ($record) => $record->fecha_puesta_marcha ? $record->fecha_puesta_marcha->translatedFormat('d, M Y'): '-'),

                    TextEntry::make('codigo')
                        ->label('Código')
                        ->state(fn ($record) => $record->codigo),

                    TextEntry::make('modelo')
                        ->label('Modelo')
                        ->state(fn ($record) => $record->modelo),

                    TextEntry::make('fabricante')
                        ->label('Fabricante')
                        ->state(fn ($record) => $record->fabricante),

                    TextEntry::make('serie')
                        ->label('N° de Serie')
                        ->state(fn ($record) => $record->serie),
                    
                    TextEntry::make('ubicacion')
                        ->columnSpanFull()
                        ->label('Ubicación')
                        ->state(fn ($record) => $record->ubicacion),
                    
                    TextEntry::make('descripcion')
                    ->html()
                        ->columnSpanFull()
                        ->label('Descripción')
                        ->state(fn ($record) => $record->descripcion),

                ]),
            
            
            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('creado_por')
                        ->size('xs')
                        ->label('')
                        ->state(fn ($record) => 
                            'Creado por ' .
                            ($record->creadoPor->name ?? '-') . 
                            ' el ' . 
                            ($record->created_at ? $record->created_at->translatedFormat('d, M Y H:i') : '-')
                        ),
                    TextEntry::make('actualizado_por')
                        ->size('xs')
                        ->label('')
                        ->state(fn ($record) => 
                            'Actualizado por ' .
                            ($record->actualizadoPor->name ?? '-') . 
                            ' el ' . 
                            ($record->updated_at ? $record->updated_at->translatedFormat('d, M Y H:i') : '-')
                        ),
                
                ]),
            ]),
        ]);
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

    

}
