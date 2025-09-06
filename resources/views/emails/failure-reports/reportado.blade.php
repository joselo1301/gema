<x-mail::message>
# Reporte {{ $reporte->numero_reporte }} reportado

Se reportó el RF **{{ $reporte->numero_reporte }}** del activo **{{ $reporte->asset?->tag }}**.
**Estado actual:** {{ $estado }}

<x-mail::button :url="$url">
Ver reporte
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
