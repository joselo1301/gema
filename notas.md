Si se clona desde github ejecutar composer install - y copiar archivo env

Se instalo blueprint  
Se instalo filament
  - Se agrego paneles
  - Se agrego usuario
Se instalo Laravel-permission (Para el UI se planea agregar Plugin Filament Spatie Roles Permissions - https://filamentphp.com/plugins/tharinda-rodrigo-spatie-roles-permissions)
  - composer require spatie/laravel-permission
Se instalo Laravel Activity Log
Se instalo Laravel-medialibrary




Cambiar .env 
  QUEUE_CONNECTION=sync #La cola de trabajos se ejecuta de forma síncrona
# QUEUE_CONNECTION=database Se activa la cola de trabajos

agrege FILAMENT_SHIELD_PANEL=gema a env

Cada ves que ejecuto migrate_refreh se debe actualizar Shield  php artisan shield:generate, php artisan shield:install gema, php artisan shield:super-admin --user=1 --panel=gema

para ver imagenes despues de guardar ejecutar:  php artisan storage:link


Actualizar si instala en otra maquina composer install --prefer-dist --no-progress -o