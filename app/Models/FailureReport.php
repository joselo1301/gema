<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class FailureReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, HasFilamentComments;

    public function addSystemComment(string $message, ?int $userId = null): void
    {
        $userId ??= Auth::user()?->id;

        $this->filamentComments()->create([
            'comment' => $message,
            'user_id' => $userId,
            'subject_type' => static::class,  // Agregamos el tipo del modelo
            'subject_id' => $this->id,        // Agregamos el ID del modelo
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Reporte de Falla')                         // canal
            ->logOnlyDirty()                               // solo si realmente cambiaron
            ->dontLogIfAttributesChangedOnly(['updated_at']) // si SOLO cambió updated_at, no loguear
            ->dontSubmitEmptyLogs()
            ;
            

    }

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

    
    public function assetStatusOnReport(): BelongsTo
    {
        // 👇 clave foránea personalizada
        return $this->belongsTo(AssetState::class, 'asset_status_on_report');
    }

    public function assetStatusOnClose(): BelongsTo
    {
        // 👇 clave foránea personalizada
        return $this->belongsTo(AssetState::class, 'asset_status_on_close');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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

    public function people()
    {
        return $this->belongsToMany(Person::class); // al ser estándar NO pasas nombre de tabla
        // ->withTimestamps(); // solo si agregaste timestamps en la pivot
    }
    
    
    public function scopeExcludeFollowupId(Builder $query, int $id): Builder
    {
        // Permite ver registros con NULL y cualquier id ≠ $id
        return $query->where(function ($q) use ($id) {
            $q->whereNull('report_followup_id')
            ->orWhere('report_followup_id', '!=', $id);
        });
    }
}
