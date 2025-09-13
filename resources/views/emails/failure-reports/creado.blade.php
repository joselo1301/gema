@extends('emails.failure-reports.base')

@section('titulo', 'REPORTE DE FALLA INGRESADO | ' . $reporte->location->nombre)

@section('contenido')



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
    {{ $reporte->creadoPor->name ?? 'N/A' }} 
    <em> - {{ $reporte->creadoPor->puesto ?? '' }} {{ $reporte->creadoPor->empresa ?? '' }}</em>
</small>


@endsection