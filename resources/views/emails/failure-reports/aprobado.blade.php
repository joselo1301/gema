@extends('emails.failure-reports.base')

@section('titulo', 'NUEVO REPORTE DE FALLA ' . $reporte->numero_reporte . ' | ' . $reporte->location->nombre)

@section('contenido')
<p>Se notifica la emisión de un nuevo reporte de falla. {{ $extra['comentario'] }}</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> {{ $reporte->numero_reporte }}</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
</ul>
<small>
    <strong>Reportado por:</strong>
    {{ $reporte->reportadoPor->name ?? 'N/A' }} 
    <em> - {{ $reporte->reportadoPor->puesto ?? '' }} {{ $reporte->reportadoPor->empresa ?? '' }}</em>
    <br> 
    <strong>Notificado por:</strong>
    {{ $reporte->aprobadoPor->name ?? 'N/A' }} 
    <em>- {{ $reporte->aprobadoPor->puesto ?? '' }} {{ $reporte->aprobadoPor->empresa ?? '' }}</em>
</small>

@endsection