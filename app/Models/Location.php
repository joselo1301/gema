<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
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
        'direccion',
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
            'activo' => 'boolean',
        ];
    }
    
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // Location NO tiene systemsCatalogs según draft.yaml
    // Eliminamos esta relación

    // Obtener solo activos raíz (padres) de esta ubicación
    public function rootAssets(): HasMany
    {
        return $this->hasMany(Asset::class)->whereNull('asset_parent_id');
    }
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'location_users')
                    ->withTimestamps();
    }
}
