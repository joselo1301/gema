import './bootstrap';

// resources/js/app.js
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';

// Re-inicialización segura para Filament/Livewire v3
let __glb;
function mountGLightbox() {
  try { __glb?.destroy(); } catch {}
  __glb = GLightbox({
    selector: '.glightbox',
    touchNavigation: true,
    loop: true,
  });
}

// Carga inicial
document.addEventListener('DOMContentLoaded', mountGLightbox);

// MUY IMPORTANTE en Livewire v3 (SPA): re-inicializar tras navegar
document.addEventListener('livewire:navigated', mountGLightbox);