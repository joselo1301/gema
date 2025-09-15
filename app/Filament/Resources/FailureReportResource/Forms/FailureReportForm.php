<?php

namespace App\Filament\Resources\FailureReportResource\Forms;

use App\Models\Asset;
use App\Models\Person;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Illuminate\Support\Facades\Auth;

class FailureReportForm
{
    public static function getForm(): array
    {
        return [
            Wizard::make([
                Step::make('Contexto y Activo')
                    ->columns(3)
                ->schema([

                
                    Select::make('asset_id')
                        ->columnSpan(2)
                        ->label('Activo')
                        
                        ->relationship('asset', 'id', function ($query, $state, $get) {
                            $user = Auth::user();
                            if ($user) {
                                $query->whereHas('location.users', function ($q) use ($user) {
                                    $q->where('users.id', $user->id);
                                });
                            }
                        })
                        ->getOptionLabelFromRecordUsing(function ($record) {
                            $locationName = $record->location?->nombre ?? '';
                            return "{$record->nombre} ({$record->tag}) - {$locationName}";
                        })
                        ->searchable(['nombre', 'tag'])
                        ->noSearchResultsMessage('Activo no encontrado...')
                        ->preload()
                        ->required(),

                    DateTimePicker::make('fecha_ocurrencia')
                        ->columnSpan(1)
                        ->label('Fecha y hora de ocurrencia')
                        ->required()
                        ->seconds(false),

                        Textarea::make('datos_generales')
                        ->columnSpanFull()
                        ->required(),

                    Textarea::make('descripcion_corta')
                        ->label('Descripción corta')
                        ->columnSpanFull()
                        ->maxLength(150)
                        ->required(),

                ]),
                Step::make('Descripción detallada')
                ->schema([
                    Select::make('personal_detector')
                        ->label('¿Quién detectó la falla?')
                        ->multiple()
                        ->relationship('people', 'id', function ($query, $state, $get) {
                            $assetId = $get('asset_id');
                            if ($assetId) {
                                $asset = Asset::find($assetId);
                                if ($asset && $asset->location_id) {
                                    $query->where('location_id', $asset->location_id);
                                }
                            }
                        })
                        ->getOptionLabelFromRecordUsing(
                            fn (Person $record) => "{$record->nombres} {$record->apellidos}" .
                                ($record->cargo ? " — {$record->cargo}" : '') .
                                ($record->empresa ? " ({$record->empresa})" : '')
                        )
                        ->createOptionForm(function ($get) {
                            $asset = $get('asset_id') ? Asset::find($get('asset_id')) : null;
                            $locationId = $asset?->location_id;

                            return [
                                Hidden::make('location_id')
                                    ->default($locationId)
                                    ->dehydrated(), // <- importante: se envía al modelo
                                TextInput::make('nombres')->required(),
                                TextInput::make('apellidos')->required(),
                                TextInput::make('cargo'),
                                TextInput::make('empresa'),
                            ];
                        })
                        // ->createOptionAction(function (\Filament\Actions\Action $action) {
                        //     return $action
                        //         // seguridad extra si quieres validar:
                        //         ->mutateFormDataUsing(function (array $data, $livewire) {
                        //             if (blank($data['location_id'] ?? null)) {
                        //                 // si no hay asset elegido, evita crear sin ubicación
                        //                 throw \Filament\Support\Exceptions\Halt::make()
                        //                     ->withMessage('Primero selecciona un Activo para asignar la ubicación del personal.');
                        //             }
                        //             return $data;
                        //         });
                        // })
                        ->preload()
                        ->searchable()
                        ->required(),
                        
                    Textarea::make('descripcion_detallada')
                        ->label('Descripción detallada')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('acciones_realizadas')
                        ->label('Acciones realizadas para controlar o eliminar la falla')
                        ->rows(4)                        
                        ->columnSpanFull()
                        ->default('1. ')
                        ->placeholder("1. Describe la primera acción\n2. Describe la segunda acción")
                        ->extraAttributes([
                            'x-on:keydown.enter.prevent' => "
                                const el = \$event.target;
                                const start = el.selectionStart;
                                const end = el.selectionEnd;
                                const before = el.value.slice(0, start);
                                const after = el.value.slice(end);
                                const linesBefore = before.split(/\\r?\\n/).filter(l => l.trim() !== '');
                                const next = linesBefore.length + 1;
                                const insert = '\\n' + next + '. ';
                                el.value = before + insert + after;
                                const caret = (before + insert).length;
                                el.selectionStart = el.selectionEnd = caret;
                                el.dispatchEvent(new Event('input'));
                            ",
                        ])

                ]),
                Step::make('Causas y Efectos')
                ->columns(2)                
                ->schema([
                    Textarea::make('causas_probables')
                        ->label('Causas probables')
                        ->rows(4)
                        ->required()
                        ->columnSpanFull()
                        ->default('1. ')
                        ->placeholder("1. Describe la primera causa probable\n2. Describe la segunda causa probable")
                        ->extraAttributes([
                            'x-on:keydown.enter.prevent' => "
                                const el = \$event.target;
                                const start = el.selectionStart;
                                const end = el.selectionEnd;
                                const before = el.value.slice(0, start);
                                const after = el.value.slice(end);
                                const linesBefore = before.split(/\\r?\\n/).filter(l => l.trim() !== '');
                                const next = linesBefore.length + 1;
                                const insert = '\\n' + next + '. ';
                                el.value = before + insert + after;
                                const caret = (before + insert).length;
                                el.selectionStart = el.selectionEnd = caret;
                                el.dispatchEvent(new Event('input'));
                            ",
                        ]),
                    Toggle::make('afecta_operaciones')
                        ->label('¿Afecta las operaciones?')
                        ->columnSpanFull()
                        ->onColor('success'),
                    Toggle::make('afecta_medio_ambiente')
                        ->label('¿Afecta el medio ambiente?')
                        // ->onIcon('heroicon-o-check')
                        // ->offIcon('heroicon-o-x-mark')
                        ->columnSpanFull()
                        ->onColor('success'),
                    // Textarea::make('apoyo_adicional'),
                    Textarea::make('observaciones')
                        ->columnSpanFull(),
                    ]),
            Step::make('Estado y Evidencias')
                ->schema([

                    Select::make('asset_status_on_report')
                        ->label('Estado del activo')
                        ->relationship('assetStatusOnReport', 'nombre', fn ($query) => $query->orderBy('orden'))
                        ->required()
                        ->helperText('El estado del activo cambiará únicamente cuando se apruebe el reporte de falla.'),
                    
                    SpatieMediaLibraryFileUpload::make('evidencias')
                        ->label('Imágenes o documentos')
                        ->collection('failure_reports')
                        ->multiple()
                        ->imageEditor()                        
                        ->maxSize(2048) // KB = 2 MB
                        ->columnSpanFull()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        

                    ]),
            
            ])
        ];
    }
}