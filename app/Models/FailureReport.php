<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Spatie\MediaLibrary\InteractsWithMedia;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Spatie\MediaLibrary\HasMedia;

class FailureReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'numero_reporte',
        'fecha_ocurrencia',
        'datos_generales',
        'descripcion_corta',
        'personal_detector',
        'descripcion_detallada',
        'causas_probables',
        'acciones_realizadas',
        'afecta_operaciones',
        'afecta_medio_ambiente',
        'apoyo_adicional',
        'observaciones',
        'asset_id',
        'asset_parent_id',
        'asset_state_id',
        'report_status_id',
        'report_followup_id',
        'creado_por_id',
        'reportado_por_id',
        'reportado_en',
        'aprobado_por_id',
        'aprobado_en',
        'ejecutado_por_id',
        'actualizado_por_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'fecha_ocurrencia' => 'datetime',
            'afecta_operaciones' => 'boolean',
            'afecta_medio_ambiente' => 'boolean',
            'asset_id' => 'integer',
            'asset_parent_id' => 'integer',
            'asset_state_id' => 'integer',
            'report_status_id' => 'integer',
            'report_followup_id' => 'integer',
            'creado_por_id' => 'integer',
            'reportado_por_id' => 'integer',
            'reportado_en' => 'datetime',
            'aprobado_por_id' => 'integer',
            'aprobado_en' => 'datetime',
            'ejecutado_por_id' => 'integer',
            'actualizado_por_id' => 'integer',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assetParent(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assetState(): BelongsTo
    {
        return $this->belongsTo(AssetState::class);
    }

    public function reportStatus(): BelongsTo
    {
        return $this->belongsTo(ReportStatus::class);
    }

    public function reportFollowup(): BelongsTo
    {
        return $this->belongsTo(ReportFollowup::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aprobadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ejecutadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

   public function detectadoPor(): BelongsToMany
    {
        return $this->belongsToMany(People::class, 'failure_report_people')
            ->withTimestamps();
    }
    

    public static function getForm(): array
    {
        return [
            Wizard::make([
                // Step::make('Contexto y Activo')
                // ->columns(3)
                // ->schema([

                //     Select::make('asset_id')
                //         ->columnSpan(2)
                //         ->label('Activo')
                //         ->relationship('asset', 'nombre')
                //         ->getOptionLabelFromRecordUsing(function ($record) {
                //             $locationName = $record->location?->nombre ?? '';
                //             return "{$record->nombre} ({$record->tag}) - {$locationName}";
                //         })
                //         ->searchable(['nombre', 'tag'])
                //         ->noSearchResultsMessage('Activo no encontrado...')
                //         ->preload()
                //         ->required(),

                //     DateTimePicker::make('fecha_ocurrencia')
                //         ->columnSpan(1)
                //         ->label('Fecha y hora de ocurrencia')
                //         ->required(),

                //         Textarea::make('datos_generales')
                //         ->columnSpanFull()
                //         ->required(),

                //     Textarea::make('descripcion_corta')
                //         ->columnSpanFull()
                //         ->required(),

                // ]),
                // Step::make('Descripción detallada')
                //     ->schema([
                //     Select::make('personal_detector')
                //         ->label('¿Quién detectó la falla?')
                //         ->multiple()
                //         ->relationship('detectadoPor', 'cargo')
                //         ->getOptionLabelFromRecordUsing(
                //             fn (People $record) => "{$record->nombres} {$record->apellidos}" .
                //                 ($record->cargo ? " — {$record->cargo}" : '')
                //         )
                //         ->createOptionForm(People::getForm())
                //         ->preload()
                //         ->searchable()
                //         ->required(),
                        
                //     Textarea::make('descripcion_detallada')
                //         ->required()
                //         ->rows(3)
                //         ->columnSpanFull(),

                //     Textarea::make('acciones_realizadas')
                //         ->rows(4)
                //         ->required()
                //         ->columnSpanFull()
                //         ->default('1. ')
                //         ->placeholder("1. Describe la primera acción\n2. Describe la segunda acción")
                //         ->extraAttributes([
                //             'x-on:keydown.enter.prevent' => "
                //                 const el = \$event.target;
                //                 const start = el.selectionStart;
                //                 const end = el.selectionEnd;
                //                 const before = el.value.slice(0, start);
                //                 const after = el.value.slice(end);
                //                 const linesBefore = before.split(/\\r?\\n/).filter(l => l.trim() !== '');
                //                 const next = linesBefore.length + 1;
                //                 const insert = '\\n' + next + '. ';
                //                 el.value = before + insert + after;
                //                 const caret = (before + insert).length;
                //                 el.selectionStart = el.selectionEnd = caret;
                //                 el.dispatchEvent(new Event('input'));
                //             ",
                //         ])

                // ]),

                // Step::make('Causas y Efectos')
                
                // ->schema([
                //     Textarea::make('causas_probables')
                //         ->rows(4)
                //         ->required()
                //         ->columnSpanFull()
                //         ->default('1. ')
                //         ->placeholder("1. Describe la primera causa probable\n2. Describe la segunda causa probable")
                //         ->extraAttributes([
                //             'x-on:keydown.enter.prevent' => "
                //                 const el = \$event.target;
                //                 const start = el.selectionStart;
                //                 const end = el.selectionEnd;
                //                 const before = el.value.slice(0, start);
                //                 const after = el.value.slice(end);
                //                 const linesBefore = before.split(/\\r?\\n/).filter(l => l.trim() !== '');
                //                 const next = linesBefore.length + 1;
                //                 const insert = '\\n' + next + '. ';
                //                 el.value = before + insert + after;
                //                 const caret = (before + insert).length;
                //                 el.selectionStart = el.selectionEnd = caret;
                //                 el.dispatchEvent(new Event('input'));
                //             ",
                //         ]),

                //     Toggle::make('afecta_operaciones'),
                    
                //     Toggle::make('afecta_medio_ambiente'),

                        
                // ]),
             Step::make('Evidencias')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('evidencias')
                        ->label('Imágenes o documentos')
                        ->collection('failure_reports')
                        ->multiple()
                        ->imageEditor()                        
                        ->maxSize(1024) // KB = 1 MB
                        ->columnSpanFull()
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        

                ]),
            Step::make('Billing')
                ->schema([
                    
                    TextInput::make('numero_reporte')
                                ->required()
                                ->maxLength(255),
                            
                    Textarea::make('apoyo_adicional')
                        ->required()
                        ->columnSpanFull(),
                    Textarea::make('observaciones')
                        ->columnSpanFull(),



                    Select::make('report_status_id')
                        ->relationship('reportStatus', 'id')
                        ->required(),
                    Select::make('report_followup_id')
                        ->relationship('reportFollowup', 'id')
                        ->required(),
                    Select::make('creado_por_id')
                        ->relationship('creadoPor', 'name')
                        ->required(),
                    Select::make('reportado_por_id')
                        ->relationship('reportadoPor', 'name'),
                    DateTimePicker::make('reportado_en'),
                    Select::make('aprobado_por_id')
                        ->relationship('aprobadoPor', 'name'),
                    DateTimePicker::make('aprobado_en'),
                    Select::make('ejecutado_por_id')
                        ->relationship('ejecutadoPor', 'name'),
                    Select::make('actualizado_por_id')
                        ->relationship('actualizadoPor', 'name'),
                ]),
            ])
        ];
    }
}
