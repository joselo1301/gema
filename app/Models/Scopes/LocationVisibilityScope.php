<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LocationVisibilityScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Evita interferir en migraciones/seeders/commands
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user(); // <-- usuario autenticado (guard por defecto)

        if (! $user) {
            return;
        }

        // Bypass para un rol con acceso total (si usas Spatie Permission)
        if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
            return;
        }

        $ids = $user->locations()->pluck('locations.id');

        if ($ids->isEmpty()) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->whereIn($model->getTable() . '.id', $ids);
    }
}
