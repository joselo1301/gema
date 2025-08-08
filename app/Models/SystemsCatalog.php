<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemsCatalog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'orden',
        'activo',
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
            'orden' => 'integer',
            'activo' => 'boolean',
        ];
    }

    // SystemsCatalog NO pertenece a Location según draft.yaml
    // Eliminamos la relación location()

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'systems_catalog_id');
    }

    // Obtener solo activos raíz (padres) de este catálogo
    public function rootAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'systems_catalog_id')->whereNull('asset_parent_id');
    }
}
