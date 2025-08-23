<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // El usuario puede ver assets solo si tiene el permiso y tiene al menos una locación asignada
        return $user->can('view_any_asset') && $user->locations()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('view_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // El usuario puede crear assets solo si tiene el permiso y tiene al menos una locación asignada
        return $user->can('create_asset') && $user->locations()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('update_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('delete_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('force_delete_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('restore_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Asset $asset): bool
    {
        // Verificar que el usuario tenga el permiso básico
        if (!$user->can('replicate_asset')) {
            return false;
        }

        // Verificar que el asset pertenezca a una locación asignada al usuario
        return $user->canAccessAsset($asset);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_asset');
    }
}
