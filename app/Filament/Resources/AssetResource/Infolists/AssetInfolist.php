<?php

namespace App\Filament\Resources\AssetResource\Infolists;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{Grid, Section, Split as SplitInfo, ImageEntry, TextEntry, IconEntry, ColorEntry, Fieldset, SpatieMediaLibraryImageEntry, Tabs, ViewEntry};

class AssetInfolist
{
    public static function getInfolist(Infolist $infolist): Infolist
    {
        return $infolist
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
                ->columns(5)
                ->schema([

                    TextEntry::make('nombre')
                        ->columnSpan(4)
                        ->size('lg')
                        ->label('')
                        ->html()
                        ->state(function ($record) {
                            $nombre = $record->nombre;
                            $parent = $record->assetParent->nombre ?? null;
                            if ($parent) {
                                return "<span style='font-size: 0.8em; color: #888;'>Activo superior: {$parent}</span> <br> <span style='font-weight: bold;'>{$nombre}</span> ";
                            }
                            return "<span style='font-weight: bold;'>{$nombre}</span>";
                        }),

                    TextEntry::make('locacion.nombre')
                    ->columnSpan(1)
                    ->alignRight()
                    ->label('')
                    ->state(fn ($record) => $record->location->nombre ?? '-'),

                    

                    
                        
                ]),
                        
                Section::make()
                ->columnSpan(3)
                ->extraAttributes(['class' => '!gap-y-1']) 
                ->schema([
                    
                    ViewEntry::make('gallery')
                            ->label('')
                            ->view('infolists.entries.glightbox-gallery')
                            ->viewData([
                                'collection' => 'assets',
                                'center' => true,
                                'sinAdjuntos' => '',
                            ]),
                    
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
            
            ]),

            TextEntry::make('activo')
                ->columnSpanFull()
                ->alignEnd()
                ->size('xs')
                ->label('')
                ->color('gray')
                ->icon(fn ($record) => $record->activo ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->iconColor(fn ($record) => $record->activo ? 'success' : 'danger') // icono verde o rojo
                ->state(fn ($record) =>
                    $record->activo
                        ? 'El activo está habilitado'
                        : 'El activo está deshabilitado'
                ),

            Section::make()
                ->columnSpanFull()
                ->extraAttributes(['class' => '!gap-y-1'])
                ->schema([

                    
                    TextEntry::make('creado_por')
                        ->size('xs')
                        ->color('gray')
                        ->state(fn ($record) =>
                            
                            ($record->creadoPor->name ?? '-') .
                            ' el ' .
                            ($record->created_at ? $record->created_at->translatedFormat('d, M Y H:i') : '-')
                        ),
                    TextEntry::make('actualizado_por')
                        ->size('xs')
                        ->color('gray')
                        ->state(fn ($record) =>
                        
                            ($record->actualizadoPor->name ?? '-') .
                            ' el ' .
                            ($record->updated_at ? $record->updated_at->translatedFormat('d, M Y H:i') : '-')
                        ),
                    
                ]),
        ]);
    }
}
