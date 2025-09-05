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
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{Grid, Section, Split as SplitInfo, ImageEntry, TextEntry, IconEntry, ColorEntry, Fieldset, RepeatableEntry, SpatieMediaLibraryImageEntry, Tabs, ViewEntry};
use Filament\Resources\RelationManagers\RelationGroup;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Parallax\FilamentComments\Tables\Actions\CommentsAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

use function Laravel\Prompts\text;

class FailureReportResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = FailureReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Reportes de falla';
    protected static ?string $modelLabel = 'Reporte de falla';
    protected static ?string $pluralModelLabel = 'Reportes de falla';


    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'reportar',
            'aprobar',
            'rechazar',
            'cambiar_etapa',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form

            ->columns(1)
            ->schema(FailureReport::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
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
                    ->sortable()
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $c = $record->reportFollowup->color ?? 'gray';

                        // ¿Color hex (#...) o clave del tema (primary, success, etc.)?
                        $isHex = is_string($c) && str_starts_with($c, '#');

                        if ($isHex) {
                            $text   = $c;
                            $border = $c;
                            $bg     = 'transparent'; // Mantén el fondo limpio para mantener legibilidad corporativa
                        } else {
                            // Colores del tema de Filament (se adaptan a light/dark)
                            $text   = "var(--{$c}-600)";
                            $border = "var(--{$c}-400)";
                            $bg     = "var(--{$c}-50)";
                        }

                                return <<<HTML
                        <span style="
                            display:inline-flex;align-items:center;gap:.5rem;
                            padding:.25rem .625rem;border:1px solid {$border};
                            border-radius:.5rem;background:{$bg};
                            color:{$text};font-weight:600;font-size:.75rem;line-height:1;
                        ">
                            <span style="
                                width:.5rem;height:.5rem;border-radius:9999px;
                                background:{$text};flex-shrink:0;
                            "></span>
                            <span style="white-space:nowrap;">{$state}</span>
                        </span>
                        HTML;
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
                    ->relationship('reportStatus', 'nombre' , fn ($query) => $query->orderBy('orden')),
                // Filtro por Sistema
                Tables\Filters\SelectFilter::make('reportFollowup_id')
                    ->label('Etapa de Reporte')
                    ->relationship('reportFollowup', 'nombre', fn ($query) => $query->orderBy('orden')),
                Tables\Filters\SelectFilter::make('location_id')
                    ->label('Ubicación')
                    ->relationship('location', 'nombre', function ($query) {
                        $user = Filament::auth()->user();
                        return $query->whereIn('id', $user->locations->pluck('id'))->orderBy('nombre');
                    })
                    ->visible(fn () => Filament::auth()->user()->locations->count() > 1),
                
            ])
            ->actions([
                ActivityLogTimelineTableAction::make('Bitácora')
                    ->label('')
                    ->tooltip('Bitácora')
                    ->withRelations(['profile', 'address'])
                    ->icon('heroicon-s-eye')
                    ->timelineIconColors([
                        'created' => 'success',
                        'updated' => 'info',                       
                    ])
                    ->limit(10),

                CommentsAction::make()
                    ->label('')
                    ->tooltip('Comentarios')
                ,
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Grid::make()
                ->columns([
                            'default' => 3,
                            'md' => 3,
                            'lg' => 3,
                            'xl' => 3,
                        ])
                ->schema([
                    // TextEntry::make('numero_reporte')
                    //     ->label('Reporte N°')
                    //     ->columnSpan(3)
                    //     ->size('lg'), // Tamaños disponibles: 'sm', 'md', 'lg', 'xl'
                   

                    Section::make(fn ($record): string => 'Reporte N° ' . ($record->numero_reporte ?? ' ***Número se asigna al aprobar***'))
                        // ->description('Datos generales del reporte')                        
                        ->columnSpanFull()
                        ->columns(4)
                        ->schema([
                            // TextEntry::make('numero_reporte')
                            //     ->label('Reporte N°'),                                
                            TextEntry::make('fecha_ocurrencia')
                                ->label('Fecha y hora de Ocurrencia')
                                ->formatStateUsing(function ($state) {
                                    if (!$state) return '';
                                    setlocale(LC_TIME, 'es_ES.UTF-8');
                                    // Carbon para formato personalizado
                                    return \Carbon\Carbon::parse($state)
                                        ->translatedFormat('l, d \d\e F \d\e\l Y H:i \h\r\s');
                                }),
                            TextEntry::make('location.nombre')
                                ->label('Planta/Terminal'),
                            TextEntry::make('reportStatus.nombre')
                                ->badge()
                                ->color(fn ($record) => $record->reportStatus?->color)
                                ->label('Estado'),
                            TextEntry::make('reportFollowup.nombre')
                                ->label('Etapa')                                
                                ->html()
                                ->formatStateUsing(function ($state, $record) {
                                    $c = $record->reportFollowup->color ?? 'gray';

                                    // ¿Color hex (#...) o clave del tema (primary, success, etc.)?
                                    $isHex = is_string($c) && str_starts_with($c, '#');

                                    if ($isHex) {
                                        $text   = $c;
                                        $border = $c;
                                        $bg     = 'transparent'; // Mantén el fondo limpio para mantener legibilidad corporativa
                                    } else {
                                        // Colores del tema de Filament (se adaptan a light/dark)
                                        $text   = "var(--{$c}-600)";
                                        $border = "var(--{$c}-400)";
                                        $bg     = "var(--{$c}-50)";
                                    }

                                            return <<<HTML
                                    <span style="
                                        display:inline-flex;align-items:center;gap:.5rem;
                                        padding:.25rem .625rem;border:1px solid {$border};
                                        border-radius:.5rem;background:{$bg};
                                        color:{$text};font-weight:600;font-size:.75rem;line-height:1;
                                    ">
                                        <span style="
                                            width:.5rem;height:.5rem;border-radius:9999px;
                                            background:{$text};flex-shrink:0;
                                        "></span>
                                        <span style="white-space:nowrap;">{$state}</span>
                                    </span>
                                    HTML;
                                }),
                       
                            
                        ]),
                        
                    Grid::make()
                        ->columns(2)
                        ->columnSpan([
                           'default' => 3,
                           'lg' => 1,
                        ])
                        ->schema([
                            Section::make('Datos del Activo')
                                // ->description(fn ($record) => 'Tag: ' . ($record->asset?->tag ?? ''))
                                ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                                ])
                                ->schema([

                                    TextEntry::make('asset.nombre')
                                        ->label('')
                                        ->badge()
                                        ->helperText(fn ($record) => $record->asset?->tag ? 'Tag: ' . $record->asset->tag : null)
                                        ->url(fn ($record) => $record->asset
                                            ? AssetResource::getUrl('view', ['record' => $record->asset])
                                            : null
                                        )                                        
                                        ->openUrlInNewTab(),

                                    // TextEntry::make('asset.codigo')
                                    //     ->label('Codigo'),

                                    TextEntry::make('asset.systemsCatalog.nombre')
                                        ->label('Area / Sistema'),

                                    TextEntry::make('asset.assetClassification.nombre')
                                        ->label('Clasificación')
                                        ->formatStateUsing(function ($state, $record) {
                                            $color = $record->asset?->assetClassification?->color ?? null;
                                            if ($state && $color) {
                                                return "<span style='color:{$color};font-weight:600'>{$state}</span>";
                                            }
                                            return $state ?? '';
                                        })
                                        ->html()
                                        ->inlineLabel(),

                                    TextEntry::make('asset.assetCriticality.nombre')
                                        ->label('Criticidad')
                                        ->formatStateUsing(function ($state, $record) {
                                            $color = $record->asset?->assetCriticality?->color ?? null;
                                            if ($state && $color) {
                                                return "<span style='color:{$color};font-weight:600'>{$state}</span>";
                                            }
                                            return $state ?? '';
                                        })
                                        ->html()
                                        ->inlineLabel(),

                                    TextEntry::make('asset.assetState.nombre')
                                        ->badge()
                                        ->color(fn ($record) => $record->asset?->assetState?->color)
                                        ->label('Estado')
                                        ->inlineLabel(),

                                    TextEntry::make('asset.parent.nombre')
                                        ->badge()
                                        ->label('Activo Superior')
                                        ->visible(fn ($record) => !empty($record->asset?->asset_parent_id)),
                                ]),

                            Section::make('Personal detector')
                                ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                                ])
                                ->schema([
                                    TextEntry::make('people')
                                    ->label('')
                                    ->icon('heroicon-o-user-circle')                                    
                                    ->listWithLineBreaks() // una línea por persona
                                    ->state(function ($record) {
                                        return $record->people->map(function ($person) {
                                            return "{$person->nombres} {$person->apellidos} – {$person->cargo} ({$person->empresa})";
                                        });
                                    }),
                                    ]),

                             Section::make('Estado del activo')
                                ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                                ])
                                ->schema([
                                    TextEntry::make('assetStatusOnReport.nombre')
                                        ->badge()
                                        ->color(fn ($record) => $record->assetStatusOnReport?->color)
                                        ->label('Al reportar')
                                        ->inlineLabel(),

                                    TextEntry::make('assetStatusOnClose.nombre')
                                        ->visible(fn ($record) => !empty($record->assetStatusOnClose))
                                        ->badge()
                                        ->color(fn ($record) => $record->assetStatusOnClose?->color)
                                        ->label('Al cerrar')
                                        ->inlineLabel(),
                                    ]),
                            ]),

                        Section::make('')
                        // ->description('Datos del activo reportado')
                        ->columnSpan([
                           'default' => 3,
                           'lg' => 2,
                        ])
                        ->schema([
                            TextEntry::make('datos_generales')
                                ->label('Datos generales'),
                            TextEntry::make('descripcion_corta')
                                ->label('Descripción corta'),
                            TextEntry::make('descripcion_detallada')
                                ->label('Descripción detallada')
                                ->prose(),
                            TextEntry::make('causas_probables')
                                ->label('Causas probables')
                                ->markdown(),
                            TextEntry::make('acciones_realizadas')
                                ->label('Acciones realizadas')
                                ->markdown(),
                            TextEntry::make('afecta_operaciones')
                                ->alignEnd()
                                ->label('')
                                ->color('gray')
                                ->icon(fn ($record) => $record->afecta_operaciones ? 'heroicon-m-exclamation-circle' : 'heroicon-m-information-circle')
                                ->iconColor(fn ($record) => $record->afecta_operaciones ? 'danger' : 'info') // icono verde o rojo
                                ->state(fn ($record) =>
                                    $record->afecta_operaciones
                                        ? 'La falla afecta las operaciones'
                                        : 'La falla no afecta las operaciones'
                                ),
                            TextEntry::make('afecta_medio_ambiente')
                                ->alignEnd()
                                ->label('')
                                ->color('gray')
                                ->icon(fn ($record) => $record->afecta_medio_ambiente ? 'heroicon-m-exclamation-circle' : 'heroicon-m-information-circle')
                                ->iconColor(fn ($record) => $record->afecta_medio_ambiente ? 'danger' : 'info') // icono verde o rojo
                                ->state(fn ($record) =>
                                    $record->afecta_medio_ambiente
                                        ? 'La falla afecta al medio ambiente'
                                        : 'La falla no afecta al medio ambiente'
                                ),
                            


                        ]),

                        Section::make('Documentos y/o imágenes')
                        // ->description('Datos del activo reportado')
                        ->columnSpan([
                            'default' => 3,
                           'lg' => 3,
                        ])
                        ->schema([
                            
                            ViewEntry::make('gallery')
                            ->label('')
                            ->view('infolists.entries.glightbox-gallery')
                            ->viewData([
                                'collection' => 'failure_reports', 
                                
                            ]),
                        ]),

                        Section::make('Usuarios')
                            ->description('Registro de creación y modificaciones')
                            ->columns(2) // distribuye en 2 columnas, puedes usar 1 si prefieres en lista vertical
                            ->collapsible() // opcional: que se pueda contraer
                            ->collapsed() // inicia contraído
                            
                            
                            ->schema([
                                TextEntry::make('creadoPor.name') // relación createdBy → User
                                    ->label('Creado por')                                    
                                    ->inlineLabel(),
                                    
                                TextEntry::make('created_at')
                                    ->label('Creado el')
                                    ->dateTime('d/m/Y H:i')
                                    ->inlineLabel(),

                                TextEntry::make('reportadoPor.name') // relación reportedBy → User
                                    ->label('Reportado por')
                                    ->inlineLabel(),
                                
                                TextEntry::make('reportado_en') // campo fecha de reporte
                                    ->label('Reportado el')
                                    ->dateTime('d/m/Y H:i')
                                    ->inlineLabel(),

                                TextEntry::make('aprobadoPor.name')
                                    ->label('Aprobado por')
                                    ->inlineLabel(),

                                TextEntry::make('aprobado_en') // fecha de aprobación
                                    ->label('Aprobado el')
                                    ->dateTime('d/m/Y H:i')
                                    ->inlineLabel(),

                                TextEntry::make('actualizadoPor.name')
                                    ->label('Modificado por')
                                    ->inlineLabel(),

                                TextEntry::make('updated_at')
                                    ->label('Última modificación')
                                    ->dateTime('d/m/Y H:i')
                                    ->inlineLabel(),

                                
                            ]),

                    ]),

                    

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Bitácora', [
                ActivitylogRelationManager::class,
            ])
            ->icon('heroicon-m-eye'), 
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
