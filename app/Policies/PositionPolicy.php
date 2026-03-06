<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Position;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('position.index');
    }

    public function view(User $user, Position $model): bool
    {
        return $user->can('position.index');
    }

    public function create(User $user): bool
    {
        return $user->can('position.create');
    }

    public function update(User $user, Position $model): bool
    {
        return $user->can('position.edit');
    }

    public function delete(User $user, Position $model): bool
    {
        return $user->can('position.destroy');
    }

    public function restore(User $user, Position $model): bool
    {
        return $user->can('position.restore');
    }

    public function forceDelete(User $user, Position $model): bool
    {
        return $user->can('position.forceDelete');
    }
}
