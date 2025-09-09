<?php

namespace App\Filament\Resources\AssetResource\Forms;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Auth;

class AssetForm
{
    public static function getForm(): array
    {
        return [

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
                            
                            RichEditor::make('descripcion')
                                ->label('Descripción')
                                ->columnSpanFull()
                                ->placeholder('Descripción del activo, características, etc.'),
                        ]),
                ]),

            // 📦 Sección inferior con relaciones
            Section::make('Clasificación Técnica')
                ->columns(3)
                ->schema([
                    Toggle::make('es_activo_dependiente')
                        ->label('¿Es un activo dependiente?')
                        ->afterStateHydrated(function (callable $set, $state, Get $get) {
                            // Si está en modo edición y tiene un padre, marcar como activo dependiente
                            if (filled($get('asset_parent_id'))) {
                                $set('es_activo_dependiente', true);
                            }
                        })
                        ->dehydrated(false)
                        ->reactive(),

                    Select::make('asset_parent_id')
                        ->label('Activo superior')  
                        ->relationship('assetParent', 'nombre')
                        ->searchable()
                        ->visible(fn (Get $get) => $get('es_activo_dependiente'))
                        ->required(fn (Get $get) => $get('es_activo_dependiente'))
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
                        ->relationship('location', 'nombre', fn ($query) => $query->whereIn('id', Auth::user()->locations->pluck('id')))
                        ->visible(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->dehydrated(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->required()
                        ->default(function () {
                            $locations = Auth::user()->locations;
                            return $locations->count() === 1 ? $locations->first()->id : null;
                        }),
                        
                    Select::make('systems_catalog_id')
                        ->label('Sistema')
                        ->relationship('systemsCatalog', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->dehydrated(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->preload()
                        ->searchable()
                        ->required(),

                    Select::make('asset_classification_id')
                        ->label('Clasificación')
                        ->relationship('assetClassification', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_dependiente')) // Desactiva si es dependiente
                        ->dehydrated(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->required(),

                    Select::make('asset_criticality_id')
                        ->label('Criticidad')
                        ->relationship('assetCriticality', 'nombre')
                        ->visible(fn (Get $get) => !$get('es_activo_dependiente')) // Desactiva si es dependiente
                        ->dehydrated(fn (Get $get) => !$get('es_activo_dependiente'))
                        ->required(),

                    Select::make('asset_state_id')
                        ->label('Estado')
                        ->relationship('assetState', 'nombre')
                        ->required(),

                    
                ]),

                Section::make('Auditoría')
                    ->columns(2)
                    ->visibleOn('view')
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

                    
            
        ];
        
    }
}
