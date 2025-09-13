@extends('emails.failure-reports.base')

@section('titulo', 'ESTADO DE REPORTE DE FALLA ' . $reporte->numero_reporte . ' ACTUALIZADO | ' . $reporte->location->nombre)

@section('titulo')
Actualización de estado del REPORTE DE FALLA - {{ $reporte->numero_reporte }} | {{ $reporte->location->nombre }}
@endsection

@section('contenido')

<strong>Anterior estado: </strong>{{ $extra['estado_anterior'] }}<br>
<strong>Nuevo estado: </strong>{{ $extra['estado_nuevo'] }}<br>
<strong>Nota: </strong>{{ $extra['comentario'] }}</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> {{ $reporte->numero_reporte }}</li>
    <li><strong>Activo:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
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
    <br> 
    <strong>Actualizado por:</strong>
    {{ $reporte->actualizadoPor->name ?? 'N/A' }} 
    <em>- {{ $reporte->actualizadoPor->puesto ?? '' }} {{ $reporte->actualizadoPor->empresa ?? '' }}</em>
</small>

@endsection