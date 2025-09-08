@extends('emails.failure-reports.base')

@section('titulo', 'Nuevo Reporte de Falla Creado')

@section('contenido')
<p>Se ha creado un nuevo reporte de falla en el sistema GEMA.</p>

<h3>Detalles del Reporte:</h3>
<ul>
    <li><strong>Número de Reporte:</strong> '{{ '***Número se asignara al aprobar***' }}'</li>
    <li><strong>Asset:</strong> {{ $reporte->asset->nombre ?? 'N/A' }}</li>
    <li><strong>Ubicación:</strong> {{ $reporte->location->nombre ?? 'N/A' }}</li>
    <li><strong>Datos generales:</strong> {{ $reporte->datos_generales ?? 'N/A' }}</li>
    <li><strong>Descripción Corta:</strong> {{ $reporte->descripcion_corta }}</li>
    <li><strong>Creado por:</strong> {{ $actor->name ?? 'Sistema' }}</li>
    <li><strong>Cargo:</strong> {{ $actor->puesto ?? 'N/A' }}</li>
    <li><strong>Fecha de Creación:</strong> {{ $reporte->created_at->format('d/m/Y H:i') }}</li>
</ul>

<p>
    <a href="{{ $url }}" style="background-color: #3B82F6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
        Ver Reporte Completo
    </a>
</p>
@endsection