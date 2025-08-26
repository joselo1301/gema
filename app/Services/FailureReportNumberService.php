<?php

// app/Services/FailureReportNumberService.php

namespace App\Services;

use App\Models\FailureReportSequence;
use App\Models\Location;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FailureReportNumberService
{
    /**
     * Genera el número en formato RF-IL01-25
     */
    public function makeNumberFor(int $locationId, ?Carbon $fecha = null): string
    {
        $fecha = $fecha ?? now();

        // 1) Obtener ubicación directamente
        /** @var \App\Models\Location $location */
        $location = Location::findOrFail($locationId);

        $loc = strtoupper($location->codigo); // IL, TAC, etc.
        $yearFull = (int) $fecha->year;              // 2025
        $year2    = substr((string) $yearFull, -2);  // "25"

        // 2) Transacción con bloqueo de fila para concurrencia
        $next = DB::transaction(function () use ($loc, $yearFull) {
            // bloquear la fila de secuencia
            $seq = FailureReportSequence::query()
                ->where('codigo_locacion', $loc)
                ->where('year', $yearFull)
                ->lockForUpdate()
                ->first();

            if (! $seq) {
                $seq = new FailureReportSequence([
                    'codigo_locacion' => $loc,
                    'year'            => $yearFull,
                    'current'         => 0,
                ]);
                $seq->save();
            }

            $next = $seq->current + 1;

            // 3 dígitos → hasta 999; si necesitas más, aumenta a 4 dígitos
            if ($next > 999) {
                throw new RuntimeException("Secuencia máxima alcanzada para {$loc}-{$yearFull} (999).");
            }

            $seq->update(['current' => $next]);

            return $next;
        });

        // 3) Formato final: RF-IL01-25
        $correlativo3 = str_pad((string) $next, 3, '0', STR_PAD_LEFT);

        return sprintf('RF-%s%s-%s', $loc, $correlativo3, $year2);
    }
}
