<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Traducciones de ActivityLog
    |--------------------------------------------------------------------------
    | Aquí defines cómo se mostrarán los nombres de los campos en el log.
    | Usa la clave exacta que aparece en el modal de cambios.
    */

    'attributes' => [

        // Campos generales
        'updated_at' => 'Fecha de Actualización',
        'created_at' => 'Fecha de Creación',

        // Campos de FailureReport
        'numero_reporte' => 'Número de Reporte',
        'asset_status_on_report' => 'Estado al Reportar',
        'asset_status_on_close' => 'Estado al Cerrar',

        // Relaciones específicas
        'reportFollowup.nombre' => 'Etapa de Seguimiento',
    ],
];
