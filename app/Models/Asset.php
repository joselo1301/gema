<?php

namespace App\Models;

use App\ActivityLog\Pipes\RenameChangeKeyPipe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Forms;
use Filament\Forms\Components\{Grid, Group, Section, TextInput, DatePicker, RichEditor, Toggle, Select, Textarea};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Get;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;



class Asset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(120)
              ->height(120)
              ->sharpen(10);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Activos')                         // canal
            ->logOnly(['nombre', 'tag', 'ubicacion', 'assetState.nombre'])          // campos que SÍ auditas
            ->logOnlyDirty()                               // solo si realmente cambiaron
            ->dontLogIfAttributesChangedOnly(['updated_at']) // si SOLO cambió updated_at, no loguear
            ->dontSubmitEmptyLogs();                       // no crear logs vacíos


    }

   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'codigo',
        'tag',
        'descripcion',
        'modelo',
        'fabricante',
        'serie',
        'ubicacion',
        'fecha_adquisicion',
        'fecha_puesta_marcha',
        'foto',
        'activo',
        'location_id',
        'systems_catalog_id',
        'asset_classification_id',
        'asset_criticality_id',
        'asset_state_id',
        'asset_parent_id',
        'creado_por_id',
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
            'fecha_adquisicion' => 'date',
            'fecha_puesta_marcha' => 'date',
            'activo' => 'boolean',
            'location_id' => 'integer',
            'systems_catalog_id' => 'integer',
            'asset_classification_id' => 'integer',
            'asset_criticality_id' => 'integer',
            'asset_state_id' => 'integer',
            'asset_parent_id' => 'integer',
            'creado_por_id' => 'integer',
            'actualizado_por_id' => 'integer',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function systemsCatalog(): BelongsTo
    {
        return $this->belongsTo(SystemsCatalog::class);
    }

    public function assetClassification(): BelongsTo
    {
        return $this->belongsTo(AssetClassification::class);
    }

    public function assetCriticality(): BelongsTo
    {
        return $this->belongsTo(AssetCriticality::class);
    }

    public function assetState(): BelongsTo
    {
        return $this->belongsTo(AssetState::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Asset::class, 'asset_parent_id');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    // Scope para obtener solo los activos raíz (sin parent)
    public function scopeRoots($query)
    {
        return $query->whereNull('asset_parent_id');
    }

    // Método para verificar si tiene hijos
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function assetParent(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_parent_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
