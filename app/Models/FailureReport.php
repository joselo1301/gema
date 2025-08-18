<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class FailureReport extends Model
{
    use HasFactory;

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
        return $this->belongsToMany(People::class);
    }
}
