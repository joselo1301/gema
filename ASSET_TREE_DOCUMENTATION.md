# Vista de Árbol de Activos - Documentación

## Descripción General

La **Vista de Árbol de Activos** es una funcionalidad avanzada del sistema GEMA que permite visualizar todos los activos de manera jerárquica y organizada. Esta vista facilita la navegación y comprensión de la estructura de activos en el sistema.

## Estructura Jerárquica

```
📁 Ubicaciones (Locations)
└── 📁 Catálogos de Sistemas (SystemsCatalogs)
    ├── 🏭 Activo Padre (Nivel 0) - Azul, texto más grande
    │   ├── ⚙️ Activo Hijo (Nivel 1) - Naranja, texto mediano
    │   ├── ⚙️ Activo Hijo (Nivel 1) - Naranja, texto mediano  
    │   └── ⚙️ Activo Hijo (Nivel 1) - Naranja, texto mediano
    │       ├── 🔩 Activo Nieto (Nivel 2) - Púrpura, texto pequeño
    │       └── 🔩 Activo Nieto (Nivel 2) - Púrpura, texto pequeño
    │           └── ⚡ Activo Bisnieto (Nivel 3+) - Gris, texto muy pequeño
    └── 🏭 Activo Padre (Nivel 0) - Azul, texto más grande
        ├── ⚙️ Activo Hijo (Nivel 1) - Naranja, texto mediano
        └── ⚙️ Activo Hijo (Nivel 1) - Naranja, texto mediano
```

### 📐 Jerarquía Visual
- **Nivel 0 (Padres)**: Icono grande azul, texto base, negrita
- **Nivel 1 (Hijos)**: Icono mediano naranja, texto pequeño, peso normal
- **Nivel 2 (Nietos)**: Icono mediano púrpura, texto pequeño
- **Nivel 3+ (Descendientes)**: Icono pequeño gris, texto muy pequeño

### 🎯 Sangría Progresiva
Cada nivel de profundidad incrementa el padding-left en 6 unidades de Tailwind (24px adicionales):
- **Nivel 0**: `pl-0` (0px) - Activos padre sin sangría
- **Nivel 1**: `pl-6` (24px) - Hijos directos
- **Nivel 2**: `pl-12` (48px) - Nietos
- **Nivel 3**: `pl-18` (72px) - Bisnietos
- **Nivel 4+**: `pl-24` (96px+) - Niveles más profundos

**Implementación**: `padding-left` aplicado directamente al nodo del activo según su nivel de profundidad.

## Funcionalidades

### 🔍 Búsqueda en Tiempo Real
- **Búsqueda instantánea**: Busca por nombre, código o TAG
- **Autoexpansión**: Expande automáticamente los nodos que contienen resultados
- **Destacado visual**: Resalta los activos que coinciden con la búsqueda

### 🗂️ Navegación Intuitiva
- **Expandir/Colapsar**: Haz clic en los iconos de flecha para expandir o colapsar grupos
- **Expandir Todo**: Botón para expandir toda la estructura de una vez
- **Colapsar Todo**: Botón para colapsar y mostrar solo el primer nivel

### 🔗 Navegación a Detalles
- **Nueva ventana**: Los activos se abren en una nueva pestaña/ventana
- **Indicador visual**: Icono que aparece al hacer hover indicando que se abrirá en nueva ventana
- **Tooltip informativo**: Mensaje descriptivo al pasar el mouse

### 📊 Información Contextual
- **Contadores**: Muestra el número de activos en cada ubicación y catálogo
- **Badges informativos**: Códigos, TAGs, estados y otros metadatos
- **Información técnica**: Fabricante, modelo, serie cuando está disponible

## Archivos del Sistema

### Backend (PHP)

#### `app/Filament/Pages/AssetTreeView.php`
**Clase principal** que maneja la lógica del árbol de activos.

**Propiedades principales:**
- `$expandedNodes`: Estado de nodos expandidos/colapsados
- `$treeData`: Datos del árbol cargados desde la base de datos
- `$search`: Término de búsqueda actual
- `$locationAssetsCache`: Cache para mejorar rendimiento

**Métodos importantes:**
- `loadTreeData()`: Carga los datos iniciales del árbol
- `toggleNode()`: Alterna el estado de expansión de nodos
- `viewAsset()`: Navega hacia un activo específico
- `expandAll()/collapseAll()`: Controlan la expansión global
- `updatedSearch()`: Maneja la búsqueda en tiempo real

#### `app/Models/Asset.php`
**Relaciones agregadas:**
- `parent()`: Relación con activo padre
- `children()`: Relación con activos hijos
- `allChildren()`: Relación recursiva para todos los descendientes
- `scopeRoots()`: Scope para activos sin padre

#### `app/Models/Location.php`
**Relaciones:**
- `assets()`: Todos los activos de la ubicación
- `rootAssets()`: Solo activos raíz (sin padre)

#### `app/Models/SystemsCatalog.php`
**Relaciones:**
- `assets()`: Todos los activos del catálogo
- `rootAssets()`: Solo activos raíz del catálogo

### Frontend (Blade Templates)

#### `resources/views/filament/pages/asset-tree-view.blade.php`
**Vista principal** del árbol de activos.

**Secciones:**
- Header con botones de control
- Barra de búsqueda y filtros
- Container principal del árbol
- Estilos CSS personalizados
- JavaScript para nueva ventana

#### `resources/views/filament/pages/partials/location-node-new.blade.php`
**Componente parcial** para renderizar nodos de ubicación.

**Características:**
- Información de la ubicación (nombre, código, dirección)
- Contador de activos totales
- Lógica de expansión/colapso
- Agrupación por catálogos de sistemas

#### `resources/views/filament/pages/partials/catalog-node-new.blade.php`
**Componente parcial** para renderizar nodos de catálogos de sistemas.

**Características:**
- Información del catálogo (nombre, código, orden)
- Contador de activos específico por ubicación
- Lista de activos del catálogo

#### `resources/views/filament/pages/partials/asset-node.blade.php`
**Componente parcial recursivo** para renderizar activos y sus hijos.

**Características:**
- Información detallada del activo
- Indicadores visuales diferenciados (padre vs hijo)
- Navegación hacia detalles
- Manejo recursivo de hijos

## Optimizaciones Implementadas

### 🚀 Rendimiento
- **Lazy Loading**: Solo carga datos cuando se necesitan
- **Cache Local**: Cache en memoria para consultas repetidas
- **Consultas Optimizadas**: Select específicos para reducir transferencia de datos
- **Contadores Eficientes**: Uso de `withCount()` para estadísticas

### 💾 Base de Datos
- **Índices Recomendados**:
  ```sql
  CREATE INDEX idx_assets_location ON assets(location_id);
  CREATE INDEX idx_assets_catalog ON assets(systems_catalog_id);
  CREATE INDEX idx_assets_parent ON assets(asset_parent_id);
  CREATE INDEX idx_assets_active ON assets(activo);
  ```

### 🎨 Experiencia de Usuario
- **Feedback Visual**: Indicadores de loading y estados
- **Transiciones Suaves**: Animaciones CSS para mejor UX
- **Responsive Design**: Adaptable a diferentes tamaños de pantalla
- **Accesibilidad**: Tooltips y indicadores claros

## Casos de Uso

### 👥 Para Operadores
- Encontrar rápidamente activos específicos
- Entender la estructura de equipos en una ubicación
- Navegar de activos padre a componentes hijos

### 👨‍💼 Para Supervisores
- Tener una vista general de todos los activos
- Identificar activos en diferentes estados
- Analizar la distribución por ubicaciones

### 🔧 Para Mantenimiento
- Localizar activos para programar mantenimientos
- Entender relaciones entre equipos principales y auxiliares
- Acceder rápidamente a información detallada

## Configuración y Personalización

### 🎛️ Configuración de Navegación
En `AssetTreeView.php`, puedes modificar:
- `$navigationSort`: Orden en el menú de navegación
- `$navigationIcon`: Icono en el menú lateral
- `$navigationLabel`: Etiqueta en el menú

### 🎨 Personalización Visual
En los archivos Blade, puedes modificar:
- Colores de los badges y indicadores
- Iconos para diferentes tipos de nodos
- Layout y espaciado
- Animaciones y transiciones

### ⚙️ Rendimiento
En `AssetTreeView.php`, puedes ajustar:
- Límites de cache
- Tiempo de debounce para búsqueda
- Campos incluidos en las consultas

## Troubleshooting

### Problema: No se muestran activos
**Solución**: Verificar que los activos tengan `activo = true` y relaciones correctas

### Problema: Búsqueda lenta
**Solución**: Verificar índices en base de datos y ajustar debounce time

### Problema: Nueva ventana no se abre
**Solución**: Verificar que JavaScript esté habilitado y no haya bloqueadores de popups

## Mantenimiento

### Actualizaciones de Datos
El árbol se actualiza automáticamente cuando:
- Se crean nuevos activos
- Se modifican relaciones padre-hijo
- Se actualizan estados de activos

### Limpieza de Cache
El cache se limpia automáticamente cuando:
- Se realiza una nueva búsqueda
- Se refresca el árbol
- Se navega entre nodos

## Extensiones Futuras

### 🔮 Funcionalidades Propuestas
- Filtros avanzados por estado, criticidad, etc.
- Exportación del árbol a PDF/Excel
- Arrastrar y soltar para reorganizar
- Vista de mapa geográfico
- Integración con códigos QR/RFID

---

**Versión**: 1.0  
**Fecha**: Agosto 2025  
**Desarrollado para**: Sistema GEMA  
**Framework**: Laravel 11 + Filament 3
