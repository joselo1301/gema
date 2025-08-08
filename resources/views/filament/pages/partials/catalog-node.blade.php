{{-- Nodo de catálogo de sistemas (SystemsCatalog) --}}
{{-- Cada catálogo contiene activos padre (raíz) --}}
<div class="tree-node py-2">
    <div class="flex items-center">
        {{-- Botón para expandir/colapsar el catálogo --}}
        <button 
            wire:click="toggleNode('system_{{ $catalog->id }}')"
            class="flex items-center w-full text-left font-medium text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 node-content"
        >
            {{-- Icono de expand/collapse --}}
            @if($catalog->rootAssets->isNotEmpty())
                @if($this->isExpanded("system_{$catalog->id}"))
                    <svg class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-2 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                @endif
            @else
                {{-- Espaciado para alinear con otros nodos cuando no hay activos --}}
                <div class="w-4 h-4 mr-2 flex-shrink-0"></div>
            @endif
            
            {{-- Icono de catálogo/sistema --}}
            <svg class="w-5 h-5 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            
            {{-- Información del catálogo --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="font-medium truncate">{{ $catalog->nombre }}</span>
                        
                        {{-- Badge con el código del catálogo --}}
                        @if($catalog->codigo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                {{ $catalog->codigo }}
                            </span>
                        @endif
                        
                        {{-- Badge con el orden si existe --}}
                        @if($catalog->orden)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                #{{ $catalog->orden }}
                            </span>
                        @endif
                    </div>
                    
                    {{-- Contador de activos en este catálogo --}}
                    <div class="flex items-center space-x-1 text-xs text-gray-500">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>{{ $this->getCatalogAssetCount($catalog) }} activos</span>
                    </div>
                </div>
            </div>
        </button>
    </div>

    {{-- Lista de activos padre/raíz (solo se muestra si el catálogo está expandido) --}}
    @if($this->isExpanded("system_{$catalog->id}") && $catalog->rootAssets->isNotEmpty())
        <div class="ml-6 border-l-2 border-gray-200 dark:border-gray-600 mt-2">
            @foreach($catalog->rootAssets as $asset)
                @include('filament.pages.partials.asset-node', ['asset' => $asset, 'level' => 0])
            @endforeach
        </div>
    @endif
    
    {{-- Mensaje cuando no hay activos en el catálogo --}}
    @if($this->isExpanded("system_{$catalog->id}") && $catalog->rootAssets->isEmpty())
        <div class="ml-6 py-2 text-sm text-gray-500 italic">
            No hay activos definidos para este catálogo de sistemas
        </div>
    @endif
</div>
