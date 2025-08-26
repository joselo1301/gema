<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ReportFollowup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'clave',
        'nombre',
        'color',
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

    // Constantes legibles (evitan números mágicos)
    public const ESTADO_INGRESADO   = 'ingresado';
    public const ESTADO_REPORTADO   = 'reportado';
    public const ESTADO_NOTIFICADO  = 'notificado';
    public const ESTADO_POR_REVISAR = 'por_revisar';
    public const ESTADO_REVISION    = 'revision';
    public const ESTADO_GABINETE    = 'gabinete';
    public const ESTADO_CT_SOLPED   = 'ct_solped';
    public const ESTADO_CONTRATACION = 'contratacion';
    public const ESTADO_PLANIFICACION = 'planificacion';
    public const ESTADO_A_PROGRAMAR = 'a_programar';
    public const ESTADO_PROGRAMADO  = 'programado';
    public const ESTADO_EN_EJECUCION = 'en_ejecucion';
    public const ESTADO_EJECUTADO   = 'ejecutado';
    public const ESTADO_OBSERVADO   = 'observado';
    public const ESTADO_NO_CORRESPONDE = 'no_corresponde';

    /**
     * Devuelve el ID a partir de la clave (cacheado).
     */
    public static function idByClave(string $clave): ?int
    {
        return Cache::remember("report_followups:id:{$clave}", now()->addMinutes(10), function () use ($clave) {
            return static::query()->where('clave', $clave)->value('id');
        });
    }
}
