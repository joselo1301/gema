{{-- Vista principal del árbol de activos --}}
<x-filament-panels::page>
    {{-- Encabezado con botones de control --}}
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Vista de Árbol de Activos
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Explore la estructura jerárquica de sus activos organizados por ubicación y sistema
                </p>
            </div>
            
            {{-- Botones de control del árbol --}}
            <div class="flex space-x-2">
                <button 
                    wire:click="expandAll"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                    Expandir Todo
                </button>
                
                <button 
                    wire:click="collapseAll"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                    Colapsar Todo
                </button>
                
                <button 
                    wire:click="refreshTree"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <svg wire:loading.remove wire:target="refreshTree" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg wire:loading wire:target="refreshTree" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="refreshTree">Refrescar</span>
                    <span wire:loading wire:target="refreshTree">Cargando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            {{-- Campo de búsqueda --}}
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-300 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Buscar por nombre, código o TAG..."
                    >
                    @if($search)
                        <button 
                            wire:click="clearSearch"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            
            {{-- Estadísticas rápidas --}}
            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    </svg>
                    <span>{{ $treeData->count() }} ubicaciones</span>
                </div>
                
                @if($search)
                    <div class="text-blue-600 dark:text-blue-400 font-medium">
                        Buscando: "{{ $search }}"
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Container principal del árbol --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        {{-- Árbol de ubicaciones --}}
        <div class="p-6">
            @if($treeData->isNotEmpty())
                {{-- Nodo raíz: Todas las ubicaciones --}}
                <div class="tree-node">
                    <div class="flex items-center mb-4">
                        <button 
                            wire:click="toggleNode('locations')"
                            class="flex items-center text-lg font-semibold text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                        >
                            {{-- Icono de expand/collapse --}}
                            @if($this->isExpanded('locations'))
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            @endif
                            
                            {{-- Icono de carpeta --}}
                            <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                            </svg>
                            
                            Ubicaciones ({{ $treeData->count() }})
                        </button>
                    </div>

                    {{-- Lista de ubicaciones (solo se muestra si está expandido) --}}
                    @if($this->isExpanded('locations'))
                        <div class="ml-6 border-l-2 border-gray-200 dark:border-gray-600">
                            @foreach($treeData as $location)
                                @include('filament.pages.partials.location-node-new', ['location' => $location])
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                {{-- Estado vacío --}}
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                        No hay activos disponibles
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        No se encontraron ubicaciones o activos activos en el sistema.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Estilos CSS personalizados para el árbol --}}
    <style>
        .tree-node {
            position: relative;
        }
        
        .tree-node::before {
            content: '';
            position: absolute;
            left: -1px;
            top: 2rem;
            width: 1px;
            height: calc(100% - 2rem);
            background-color: #e5e7eb;
        }
        
        .dark .tree-node::before {
            background-color: #4b5563;
        }
        
        .tree-node:last-child::before {
            height: 0;
        }
        
        .node-content {
            transition: all 0.2s ease;
        }
        
        .node-content:hover {
            background-color: #f9fafb;
            border-radius: 0.375rem;
            padding: 0.25rem;
            margin: -0.25rem;
        }
        
        .dark .node-content:hover {
            background-color: #374151;
        }
    </style>

    {{-- Script JavaScript para abrir URLs en nueva ventana --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Método 1: Escuchar el evento Livewire personalizado
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('open-url-in-new-window', (event) => {
                    // Abrir la URL en una nueva ventana/pestaña
                    window.open(event.url, '_blank', 'noopener,noreferrer');
                });
            });
        });
        
        // Método 2: Función global para abrir URL (respaldo)
        function openAssetInNewWindow(url) {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    </script>
</x-filament-panels::page>
