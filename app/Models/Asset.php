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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;




class Asset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, HasFilamentComments;

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
}
