<x-mail::message>
# @yield('titulo', 'Notificación de GEMA')

@yield('contenido')

Saludos,<br>
**{{ config('app.name') }} - Sistema de Gestión de Mantenimiento**

---
<small>Este es un mensaje automático del sistema GEMA. No responda a este correo.</small>
</x-mail::message>
