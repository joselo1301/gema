<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Table;
use Filament\Forms\Components\{Grid, Group, Section, TextInput, DatePicker, Toggle, Select, Textarea};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Get;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;


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
        Grid::make()
            ->columns([
                'default' => 12,
                'md' => 12,
                'lg' => 12,
                'xl' => 12,
            ])
            ->schema([

                // 📸 Columna izquierda: imagen + estado
                Group::make()
                    ->columnSpan(3)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('foto')
                            ->collection('assets')
                            ->maxSize(2048) 
                            ->imageEditor()
                            ->previewable()
                            ->image() 
                            ->maxFiles(1)
                            ->columnSpanFull(),

                        Toggle::make('activo')
                            ->label('Habilitado')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success'),
                    ]),

                // 📋 Columna derecha: datos principales
                Group::make()
                    ->columnSpan(9)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre del Activo')
                                    ->required(),

                                TextInput::make('codigo')
                                    ->label('Código')
                                    ->required(),

                                TextInput::make('tag')
                                    ->label('Tag')
                                    ->required(),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('modelo')
                                    ->label('Modelo'),

                                TextInput::make('fabricante')
                                    ->label('Fabricante'),

                                TextInput::make('serie')
                                    ->label('N° de Serie'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Textarea::make('ubicacion')
                                    ->label('Referencia de Ubicación'),

                                DatePicker::make('fecha_adquisicion')
                                    ->label('Fecha de Adquisición'),

                                DatePicker::make('fecha_puesta_marcha')
                                    ->label('Fecha de Puesta en Marcha'),
                            ]),
                    ]),
            ]),

            // 📦 Sección inferior con relaciones
            Section::make('Clasificación Técnica')
                ->columns(3)
                ->schema([
                    Toggle::make('es_activo_hijo')
                        ->label('¿Es activo hijo?')
                        ->afterStateHydrated(function (callable $set, $state, Get $get) {
                            // Si está en modo edición y tiene un padre, marcar como activo hijo
                            if (filled($get('asset_parent_id'))) {
                                $set('es_activo_hijo', true);
                            }
                        })
                        ->dehydrated(false)
                        ->reactive(),

                    Select::make('asset_parent_id')
                        ->label('Activo Padre')
                        ->relationship('assetParent', 'nombre')
                        ->searchable()
                        ->visible(fn (Get $get) => $get('es_activo_hijo'))
                        ->required(fn (Get $get) => $get('es_activo_hijo'))
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $parent = Asset::find($state);

                            if ($parent) {
                                $set('location_id', $parent->location_id);
                                $set('systems_catalog_id', $parent->systems_catalog_id);
                                $set('asset_classification_id', $parent->asset_classification_id);
                                $set('asset_criticality_id', $parent->asset_criticality_id);
                            }
                        }),

                    Select::make('location_id')
                        ->label('Centro')
                        ->relationship('location', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_hijo'))
                        ->dehydrated(fn (Get $get) => !$get('es_activo_hijo'))
                        ->required(),
                        
                    Select::make('systems_catalog_id')
                        ->label('Sistema')
                        ->relationship('systemsCatalog', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_hijo'))
                        ->dehydrated(fn (Get $get) => !$get('es_activo_hijo'))
                        ->preload()
                        ->searchable()
                        ->required(),

                    Select::make('asset_classification_id')
                        ->label('Clasificación')
                        ->relationship('assetClassification', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_hijo')) // Desactiva si es hijo
                        ->dehydrated(fn (Get $get) => !$get('es_activo_hijo'))
                        ->required(),

                    Select::make('asset_criticality_id')
                        ->label('Criticidad')
                        ->relationship('assetCriticality', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_hijo')) // Desactiva si es hijo
                        ->dehydrated(fn (Get $get) => !$get('es_activo_hijo'))
                        ->required(),

                    Select::make('asset_state_id')
                        ->label('Estado')
                        ->relationship('assetState', 'nombre')
                        ->required(),

                    
                ]),

            Section::make('Auditoría')
                ->columns(2)
                ->visibleOn('edit')
                ->schema([
                    Select::make('creado_por_id')
                        ->label('Creado por')
                        ->relationship('creadoPor', 'name')
                        ->dehydrated(false)
                        ->disabled(),

                    Select::make('actualizado_por_id')
                        ->label('Actualizado por')
                        ->relationship('actualizadoPor', 'name')
                        ->dehydrated(false)
                        ->disabled(),
                ]),

                
            ]);

           
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
