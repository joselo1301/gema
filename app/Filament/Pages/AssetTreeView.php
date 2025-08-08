<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Location;
use App\Models\SystemsCatalog;
use App\Models\Asset;
use App\Filament\Resources\AssetResource;
use Illuminate\Support\Collection;

class AssetTreeView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $title = 'Vista de Árbol de Activos';
    protected static ?string $navigationLabel = 'Árbol de Activos';
    protected static string $view = 'filament.pages.asset-tree-view';
    protected static ?int $navigationSort = 1;

    // Propiedad para almacenar el estado de nodos expandidos/colapsados
    public array $expandedNodes = [];
    
    // Propiedad para almacenar el árbol de datos
    public Collection $treeData;
    
    // Cache para mejorar rendimiento
    public array $locationAssetsCache = [];
    public array $catalogCountsCache = [];
    
    // Propiedades para búsqueda y filtros
    public string $search = '';
    public array $selectedLocations = [];
    public array $selectedCatalogs = [];
    
    // Propiedades de estado
    public bool $isLoading = false;

    /**
     * Método que se ejecuta al cargar la página
     * Inicializa los datos del árbol y algunos nodos expandidos por defecto
     */
    public function mount(): void
    {
        // Cargar los datos del árbol al montar la página
        $this->loadTreeData();
        
        // Por defecto, expandir el primer nivel (Locations)
        $this->expandedNodes = [
            'locations' => true
        ];
    }

    /**
     * Carga todos los datos necesarios para construir el árbol
     * Nueva estructura optimizada: Location -> Assets agrupados por SystemsCatalog -> Asset Hijos (lazy loading)
     */
    protected function loadTreeData(): void
    {
        // Solo cargar ubicaciones inicialmente para mejorar rendimiento
        $this->treeData = Location::select(['id', 'codigo', 'nombre', 'direccion', 'activo'])
            ->where('activo', true)
            ->withCount(['assets as total_assets' => function ($query) {
                $query->where('activo', true);
            }])
            ->orderBy('nombre')
            ->get();
            
        // Limpiar caches
        $this->locationAssetsCache = [];
        $this->catalogCountsCache = [];
    }

    /**
     * Alternar el estado de expansión de un nodo específico
     * @param string $nodeId Identificador único del nodo (ej: "location_1", "system_2", "asset_3")
     */
    public function toggleNode(string $nodeId): void
    {
        // Si el nodo está expandido, colapsarlo; si está colapsado, expandirlo
        $this->expandedNodes[$nodeId] = !($this->expandedNodes[$nodeId] ?? false);
    }

    /**
     * Verifica si un nodo específico está expandido
     * @param string $nodeId Identificador del nodo
     * @return bool True si está expandido, false si está colapsado
     */
    public function isExpanded(string $nodeId): bool
    {
        return $this->expandedNodes[$nodeId] ?? false;
    }

    /**
     * Expandir todos los nodos del árbol
     * Útil para mostrar toda la estructura de una vez
     */
    public function expandAll(): void
    {
        $this->expandedNodes = [];
        
        // Expandir nivel de ubicaciones
        $this->expandedNodes['locations'] = true;
        
        foreach ($this->treeData as $location) {
            // Expandir cada ubicación
            $this->expandedNodes["location_{$location->id}"] = true;
            
            // Expandir cada catálogo de sistemas dentro de la ubicación
            $systemsCatalogs = $this->getSystemsCatalogsForLocation($location);
            foreach ($systemsCatalogs as $catalogId) {
                $this->expandedNodes["system_{$catalogId}_location_{$location->id}"] = true;
            }
            
            // Expandir cada activo raíz de la ubicación
            foreach ($location->rootAssets as $asset) {
                // Expandir cada activo y sus hijos recursivamente
                $this->expandAssetAndChildren($asset);
            }
        }
    }

    /**
     * Colapsar todos los nodos del árbol
     * Útil para mostrar solo el primer nivel
     */
    public function collapseAll(): void
    {
        $this->expandedNodes = [
            'locations' => true // Mantener solo el nivel de ubicaciones expandido
        ];
    }

    /**
     * Método recursivo para expandir un activo y todos sus hijos
     * @param Asset $asset El activo a expandir
     */
    private function expandAssetAndChildren(Asset $asset): void
    {
        $this->expandedNodes["asset_{$asset->id}"] = true;
        
        // Recursivamente expandir todos los hijos
        foreach ($asset->children as $child) {
            $this->expandAssetAndChildren($child);
        }
    }

    /**
     * Navegar hacia la vista de detalle de un activo específico
     * @param int $assetId ID del activo a visualizar
     */
    public function viewAsset(int $assetId): void
    {
        // Generar la URL del recurso
        $url = AssetResource::getUrl('view', ['record' => $assetId], panel: 'gema');
        
        // Abrir en nueva ventana usando JavaScript
        $this->dispatch('open-url-in-new-window', url: $url);
    }

    /**
     * Obtener el conteo de activos por ubicación
     * @param Location $location La ubicación
     * @return int Total de activos en la ubicación
     */
    public function getLocationAssetCount(Location $location): int
    {
        return $location->assets()->where('activo', true)->count();
    }

    /**
     * Obtener el conteo de activos por catálogo de sistemas en una ubicación específica
     * @param SystemsCatalog $catalog El catálogo
     * @param Location $location La ubicación
     * @return int Total de activos en el catálogo para esa ubicación
     */
    public function getCatalogAssetCountForLocation(SystemsCatalog $catalog, Location $location): int
    {
        return $catalog->assets()
            ->where('location_id', $location->id)
            ->where('activo', true)
            ->count();
    }

    /**
     * Obtener los IDs de catálogos de sistemas que tienen activos en una ubicación
     * @param Location $location La ubicación
     * @return array Array de IDs de catálogos
     */
    public function getSystemsCatalogsForLocation(Location $location): array
    {
        return Asset::where('location_id', $location->id)
            ->where('activo', true)
            ->whereNotNull('systems_catalog_id')
            ->distinct('systems_catalog_id')
            ->pluck('systems_catalog_id')
            ->toArray();
    }

    /**
     * Obtener activos raíz de una ubicación agrupados por catálogo de sistemas (con cache)
     * @param Location $location La ubicación
     * @param int $catalogId El ID del catálogo
     * @return Collection Colección de activos
     */
    public function getAssetsForLocationAndCatalog(Location $location, int $catalogId): Collection
    {
        $cacheKey = "location_{$location->id}_catalog_{$catalogId}";
        
        if (!isset($this->locationAssetsCache[$cacheKey])) {
            $this->locationAssetsCache[$cacheKey] = Asset::select([
                'id', 'nombre', 'codigo', 'tag', 'fabricante', 'modelo', 'serie', 
                'location_id', 'systems_catalog_id', 'asset_parent_id', 'asset_state_id'
            ])
            ->where('location_id', $location->id)
            ->where('systems_catalog_id', $catalogId)
            ->whereNull('asset_parent_id')
            ->where('activo', true)
            ->with([
                'assetState:id,nombre',
                'children' => function ($query) {
                    $query->select([
                        'id', 'nombre', 'codigo', 'tag', 'asset_parent_id', 'asset_state_id'
                    ])->where('activo', true);
                }
            ])
            ->orderBy('nombre')
            ->get();
        }
        
        return $this->locationAssetsCache[$cacheKey];
    }

    /**
     * Obtener el conteo de activos por catálogo de sistemas
     * @param SystemsCatalog $catalog El catálogo
     * @return int Total de activos en el catálogo
     */
    public function getCatalogAssetCount(SystemsCatalog $catalog): int
    {
        return $catalog->assets()->where('activo', true)->count();
    }

    /**
     * Obtener el conteo de hijos de un activo
     * @param Asset $asset El activo padre
     * @return int Total de hijos directos del activo
     */
    public function getAssetChildrenCount(Asset $asset): int
    {
        return $asset->children()->where('activo', true)->count();
    }

    /**
     * Refrescar los datos del árbol
     * Útil después de cambios en los datos
     */
    public function refreshTree(): void
    {
        $this->isLoading = true;
        
        $this->loadTreeData();
        
        $this->isLoading = false;
        
        // Mostrar notificación de éxito
        $this->dispatch('notify', [
            'message' => 'Árbol de activos actualizado correctamente',
            'type' => 'success'
        ]);
    }

    /**
     * Buscar activos en tiempo real
     */
    public function updatedSearch(): void
    {
        // Limpiar cache cuando se actualiza la búsqueda
        $this->locationAssetsCache = [];
        
        if (strlen($this->search) >= 2) {
            $this->expandSearchResults();
        }
    }

    /**
     * Expandir nodos que contienen resultados de búsqueda
     */
    protected function expandSearchResults(): void
    {
        if (empty($this->search)) return;

        // Buscar activos que coincidan con la búsqueda
        $matchingAssets = Asset::where('activo', true)
            ->where(function ($query) {
                $query->where('nombre', 'like', "%{$this->search}%")
                      ->orWhere('codigo', 'like', "%{$this->search}%")
                      ->orWhere('tag', 'like', "%{$this->search}%");
            })
            ->with(['location', 'systemsCatalog'])
            ->get();

        // Expandir automáticamente los nodos necesarios
        $this->expandedNodes['locations'] = true;
        
        foreach ($matchingAssets as $asset) {
            if ($asset->location) {
                $this->expandedNodes["location_{$asset->location->id}"] = true;
            }
            if ($asset->systemsCatalog && $asset->location) {
                $this->expandedNodes["system_{$asset->systemsCatalog->id}_location_{$asset->location->id}"] = true;
            }
        }
    }

    /**
     * Limpiar búsqueda y filtros
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->selectedLocations = [];
        $this->selectedCatalogs = [];
        $this->locationAssetsCache = [];
        
        // Colapsar todo excepto ubicaciones
        $this->expandedNodes = ['locations' => true];
    }

    /**
     * Verificar si un activo coincide con la búsqueda actual
     */
    public function matchesSearch($asset): bool
    {
        if (empty($this->search)) return true;
        
        $searchLower = strtolower($this->search);
        
        return str_contains(strtolower($asset->nombre), $searchLower) ||
               str_contains(strtolower($asset->codigo ?? ''), $searchLower) ||
               str_contains(strtolower($asset->tag ?? ''), $searchLower);
    }
}
