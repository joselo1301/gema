@extends('emails.failure-reports.base')

@section('titulo')
Actualización de estado del REPORTE DE FALLA - {{ $reporte->numero_reporte }} | {{ $reporte->location->nombre }}
@endsection

@section('contenido')

<p>{{ $extra['comentario'] }}</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> {{ $reporte->numero_reporte }}</li>
    <li><strong>Estado anterior:</strong> {{ $extra['estado_anterior'] }}</li>
    <li><strong>Nuevo estado:</strong> {{ $extra['estado_nuevo'] }}</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
    <li><strong>Reportado por:</strong> {{ $reporte->reportadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->reportadoPor->puesto . ' - ' . $reporte->reportadoPor->empresa }}</li>    
    <li><strong>Actualizado por:</strong> {{ $reporte->actualizadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->actualizadoPor->puesto . ' - ' . $reporte->actualizadoPor->empresa }}</li>    
</ul>

@endsection