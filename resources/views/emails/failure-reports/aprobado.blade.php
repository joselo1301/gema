@extends('emails.failure-reports.base')

@section('titulo')
Reporte de Falla - {{ $reporte->numero_reporte }} | {{ $reporte->location->nombre }}
@endsection

@section('contenido')
<p>Srs. JPCM, se notifica un nuevo reporte de falla en {{ $reporte->location->nombre}}.</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ $reporte->numero_reporte }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
    <li><strong>Reportado por:</strong> {{ $reporte->reportadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->reportadoPor->puesto . ' - ' . $reporte->reportadoPor->empresa }}</li>    
    <li><strong>Aprobado por:</strong> {{ $reporte->aprobadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->aprobadoPor->puesto . ' - ' . $reporte->aprobadoPor->empresa }}</li>    
</ul>

@endsection