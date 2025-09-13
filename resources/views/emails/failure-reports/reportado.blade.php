@extends('emails.failure-reports.base')

@section('titulo', 'REPORTE DE FALLA REMITIDO PARA REVISIÓN Y APROBACIÓN | ' . $reporte->location->nombre)

@section('contenido')
<strong>Reportado por:</strong>
{{ $reporte->reportadoPor->name ?? 'N/A' }} 
<em> - {{ $reporte->reportadoPor->puesto ?? '' }} {{ $reporte->reportadoPor->empresa ?? '' }}</em>
<p>{{ $extra['comentario'] }}</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ '***Número se asignara al aprobar***' }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
</ul>
<small>
    <strong>Creado por:</strong>
    {{ $reporte->CreadoPor->name ?? 'N/A' }} 
    <em> - {{ $reporte->CreadoPor->puesto ?? '' }} {{ $reporte->CreadoPor->empresa ?? '' }}</em>
</small>

@endsection