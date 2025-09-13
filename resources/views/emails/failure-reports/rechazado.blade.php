@extends('emails.failure-reports.base')

@section('titulo', 'REPORTE DE FALLA RECHAZADO | ' . $reporte->location->nombre)

@section('contenido')

<strong>Rechazado por:</strong>
{{ $reporte->actualizadoPor->name ?? 'N/A' }} 
<em>- {{ $reporte->actualizadoPor->puesto ?? '' }} {{ $reporte->actualizadoPor->empresa ?? '' }}</em><br>
<strong>Motivo: </strong>{{ $extra['comentario'] }}
    
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
    <br> 
    <strong>Reportado por:</strong>
    {{ $extra['reportado_por_nombre'] ?? 'N/A' }} 
    <em> - {{ $extra['reportado_por_puesto'] ?? '' }} {{ $extra['reportado_por_empresa'] ?? '' }}</em>
</small>


@endsection