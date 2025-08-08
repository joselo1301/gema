{{-- Nodo de ubicación (Location) --}}
{{-- Cada ubicación contiene catálogos de sistemas --}}
<div class="tree-node py-2">
    <div class="flex items-center">
        {{-- Botón para expandir/colapsar la ubicación --}}
        <button 
            wire:click="toggleNode('location_{{ $location->id }}')"
            class="flex items-center w-full text-left font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 node-content"
        >
            {{-- Icono de expand/collapse --}}
            @if($location->systemsCatalogs->isNotEmpty())
                @if($this->isExpanded("location_{$location->id}"))
                    <svg class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            @else
                {{-- Espaciado para alinear con otros nodos cuando no hay hijos --}}
                <div class="w-4 h-4 mr-2 flex-shrink-0"></div>
            @endif
            
            {{-- Icono de ubicación --}}
            <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            
            {{-- Información de la ubicación --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="font-medium truncate">{{ $location->nombre }}</span>
                        
                        {{-- Badge con el código de la ubicación --}}
                        @if($location->codigo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $location->codigo }}
                            </span>
                        @endif
                    </div>
                    
                    {{-- Contador de activos en esta ubicación --}}
                    <div class="flex items-center space-x-1 text-xs text-gray-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span>{{ $this->getLocationAssetCount($location) }} activos</span>
                    </div>
                </div>
                
                {{-- Dirección de la ubicación (si existe) --}}
                @if($location->direccion)
                    <div class="text-xs text-gray-500 mt-1 truncate">
                        {{ $location->direccion }}
                    </div>
                @endif
            </div>
        </button>
    </div>

    {{-- Lista de catálogos de sistemas (solo se muestra si la ubicación está expandida) --}}
    @if($this->isExpanded("location_{$location->id}") && $location->systemsCatalogs->isNotEmpty())
        <div class="ml-6 border-l-2 border-gray-200 dark:border-gray-600 mt-2">
            @foreach($location->systemsCatalogs as $catalog)
                @include('filament.pages.partials.catalog-node', ['catalog' => $catalog])
            @endforeach
        </div>
    @endif
    
    {{-- Mensaje cuando no hay catálogos de sistemas --}}
    @if($this->isExpanded("location_{$location->id}") && $location->systemsCatalogs->isEmpty())
        <div class="ml-6 py-2 text-sm text-gray-500 italic">
            No hay catálogos de sistemas definidos para esta ubicación
        </div>
    @endif
</div>
