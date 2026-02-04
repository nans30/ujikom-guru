<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Approval;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('approval.index');
    }

    public function view(User $user, Approval $model): bool
    {
        return $user->can('approval.index');
    }

    public function create(User $user): bool
    {
        return $user->can('approval.create');
    }

    public function update(User $user, Approval $model): bool
    {
        return $user->can('approval.edit');
    }

    public function delete(User $user, Approval $model): bool
    {
        return $user->can('approval.destroy');
    }

    public function restore(User $user, Approval $model): bool
    {
        return $user->can('approval.restore');
    }

    public function forceDelete(User $user, Approval $model): bool
    {
        return $user->can('approval.forceDelete');
    }
}
