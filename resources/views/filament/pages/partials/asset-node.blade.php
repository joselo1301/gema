{{-- Nodo de activo (Asset) - Recursivo para manejar jerarquía padre-hijos --}}
{{-- $asset: El activo actual --}}
{{-- $level: Nivel de profundidad (0 = padre, 1+ = hijos) --}}
@php
    // Calcular sangría en píxeles para este nodo según su nivel
    $indentPixels = $level * 24; // 0px, 24px, 48px, 72px, etc.
@endphp
<div class="tree-node py-1" style="padding-left: {{ $indentPixels }}px;">
    <div class="flex items-center">
        {{-- Contenedor del activo con hover y click --}}
        <div class="flex items-center w-full group">
            {{-- Botón para expandir/colapsar (solo si tiene hijos) --}}
            @if($asset->children->isNotEmpty())
                <button 
                    wire:click="toggleNode('asset_{{ $asset->id }}')"
                    class="flex items-center mr-1 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
                >
                    @if($this->isExpanded("asset_{$asset->id}"))
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    @else
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                </button>
            @else
                {{-- Espaciado cuando no hay hijos --}}
                <div class="w-6 h-6 flex-shrink-0"></div>
            @endif
            
            {{-- Botón principal del activo (para navegar) --}}
            <button 
                wire:click="viewAsset({{ $asset->id }})"
                class="flex items-center flex-1 min-w-0 p-2 rounded transition-all duration-200 node-content group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 hover:shadow-sm hover:cursor-pointer @if($this->matchesSearch($asset) && !empty($this->search)) bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 @endif"
                title="Clic para ver detalles del activo en nueva ventana"
            >
                {{-- Icono del activo (diferente según el nivel de profundidad) --}}
                @if($level === 0)
                    {{-- Icono para activos padre (nivel 0) --}}
                    <svg class="w-5 h-5 mr-3 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                @elseif($level === 1)
                    {{-- Icono para activos hijo nivel 1 --}}
                    <svg class="w-4 h-4 mr-3 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                @elseif($level === 2)
                    {{-- Icono para activos hijo nivel 2 --}}
                    <svg class="w-4 h-4 mr-3 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                @else
                    {{-- Icono para activos hijo nivel 3+ (más profundo) --}}
                    <svg class="w-3 h-3 mr-3 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                    </svg>
                @endif
                
                {{-- Información del activo --}}
                <div class="flex-1 min-w-0 text-left">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 min-w-0">
                            {{-- Nombre del activo (tamaño según nivel de profundidad) --}}
                            @php
                                $textSizeClass = match($level) {
                                    0 => 'text-base font-semibold', // Nivel 0: más grande y más destacado
                                    1 => 'text-sm font-medium',     // Nivel 1: mediano
                                    2 => 'text-sm font-normal',     // Nivel 2: más pequeño
                                    default => 'text-xs font-normal' // Nivel 3+: el más pequeño
                                };
                            @endphp
                            <span class="{{ $textSizeClass }} text-gray-900 dark:text-gray-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                {{ $asset->nombre }}
                            </span>
                            
                            {{-- Indicador temporal de nivel para debugging --}}
                            
                            
                            {{-- Badge con el código del activo --}}
                            {{-- @if($asset->codigo)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 flex-shrink-0">
                                    {{ $asset->codigo }}
                                </span>
                            @endif --}}
                            
                            {{-- Badge con el TAG si existe --}}
                            @if($asset->tag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 flex-shrink-0">
                                    TAG: {{ $asset->tag }}
                                </span>
                            @endif

                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-mono bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300">
                                N{{ $level }}
                            </span>
                        </div>
                        
                        {{-- Información adicional y contador de hijos --}}
                        <div class="flex items-center space-x-2 text-xs text-gray-500 flex-shrink-0">
                            {{-- Estado del activo --}}
                            @if($asset->assetState)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                    {{ $asset->assetState->nombre }}
                                </span>
                            @endif
                            
                            {{-- Contador de hijos si los tiene --}}
                            @if($asset->children->isNotEmpty())
                                <div class="flex items-center space-x-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span>{{ $this->getAssetChildrenCount($asset) }} hijos</span>
                                </div>
                            @endif
                            
                            {{-- Icono de abrir en nueva ventana --}}
                            <div class="flex items-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Información adicional del activo --}}
                    <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                        {{-- Fabricante y modelo --}}
                        @if($asset->fabricante || $asset->modelo)
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span>{{ $asset->fabricante }} {{ $asset->modelo }}</span>
                            </div>
                        @endif
                        
                        {{-- Serie --}}
                        @if($asset->serie)
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                <span>S/N: {{ $asset->serie }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </button>
        </div>
    </div>

    {{-- Lista de activos hijos (recursiva) - Solo se muestra si el activo está expandido --}}
    @if($this->isExpanded("asset_{$asset->id}") && $asset->children->isNotEmpty())
        {{-- Contenedor simple sin sangría adicional ya que cada hijo maneja su propia sangría --}}
        <div class="border-l-2 border-gray-200 dark:border-gray-600 ml-4 mt-1">
            @foreach($asset->children as $child)
                {{-- Llamada recursiva para renderizar los activos hijos --}}
                @include('filament.pages.partials.asset-node', ['asset' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
