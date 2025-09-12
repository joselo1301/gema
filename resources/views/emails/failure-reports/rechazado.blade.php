@extends('emails.failure-reports.base')

@section('titulo')
REPORTE DE FALLA rechazado | {{ $reporte->location->nombre }}
@endsection

@section('contenido')
<strong>Motivo:</strong>
<br>
<p>{{ $extra['comentario'] }}</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ $reporte->numero_reporte }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Fecha de ocurrencia:</strong> {{ $reporte->fecha_ocurrencia->format('d/m/Y H:i') }}</li>
    <li><strong>Rechazado por:</strong> {{ $reporte->actualizadoPor->name}}</li>
    <li><strong>Cargo:</strong> {{ $reporte->actualizadoPor->puesto . ' - ' . $reporte->actualizadoPor->empresa }}</li>    
</ul>

@endsection