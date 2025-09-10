@extends('emails.failure-reports.base')

@section('titulo', 'Nuevo Reporte de Falla' . ' | ' . $reporte->location->nombre)

@section('contenido')
<p>Se ha generado un nuevo reporte de falla en {{ $reporte->location->nombre}}.</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ '***Número se asignara al aprobar***' }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
    <li><strong>Reportado por:</strong> {{ $reporte->reportadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->reportadoPor->puesto . ' - ' . $reporte->reportadoPor->empresa }}</li>    
</ul>

@endsection