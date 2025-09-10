@extends('emails.failure-reports.base')

@section('titulo', 'Reporte de Falla creado' . ' | ' . $reporte->location->nombre)

@section('contenido')
<p>Se ha creado un nuevo reporte de falla en {{ $reporte->location->nombre}} </p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ '***Número se asignara al aprobar***' }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha y hora de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
    <li><strong>Creado por:</strong> {{ $actor->name }}</li>
    <li><strong>Cargo:</strong> {{ $actor->puesto . ' - ' . $actor->empresa }}</li>
    <li><strong>Fecha de Creación:</strong> {{ $reporte->created_at->format('d/m/Y H:i') }}</li>
</ul>

@endsection