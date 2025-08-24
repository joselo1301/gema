<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUserLocations
{
    protected static function bootBelongsToUserLocations(): void
    {
        static::addGlobalScope('user_locations', function (Builder $builder) {
            if (app()->runningInConsole()) {
                return;
            }

            $user = Auth::user(); // <-- guard por defecto

            if (! $user) {
                return;
            }

            if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                return;
            }

            $ids = $user->locations()->pluck('locations.id');

            if ($ids->isEmpty()) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $table = $builder->getModel()->getTable();
            $builder->whereIn($table . '.location_id', $ids);
        });
    }
}
