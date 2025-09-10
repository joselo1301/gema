<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FailureReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class FailureReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_failure::report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FailureReport $failureReport): bool
    {
        return $user->can('view_failure::report');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_failure::report');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FailureReport $failureReport): bool
    {
        return $user->can('update_failure::report');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FailureReport $failureReport): bool
    {
        return $user->can('delete_failure::report');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_failure::report');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, FailureReport $failureReport): bool
    {
        return $user->can('force_delete_failure::report');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_failure::report');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, FailureReport $failureReport): bool
    {
        return $user->can('restore_failure::report');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_failure::report');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, FailureReport $failureReport): bool
    {
        return $user->can('replicate_failure::report');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_failure::report');
    }

     public function reportar(User $user)
    {
        return $user->can('reportar_failure::report');
    }

    public function rechazar(User $user)
    {
        return $user->can('rechazar_failure::report');
    }

    public function aprobar(User $user)
    {
        return $user->can('aprobar_failure::report');
    }
    
    public function cambiarEtapa(User $user)
    {
        return $user->can('cambiar_etapa_failure::report');
    }
}
