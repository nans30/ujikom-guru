<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Point;
use Illuminate\Auth\Access\HandlesAuthorization;

class PointPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('point.index');
    }

    public function view(User $user, Point $model): bool
    {
        return $user->can('point.index');
    }

    public function create(User $user): bool
    {
        return $user->can('point.create');
    }

    public function update(User $user, Point $model): bool
    {
        return $user->can('point.edit');
    }

    public function delete(User $user, Point $model): bool
    {
        return $user->can('point.destroy');
    }

    public function restore(User $user, Point $model): bool
    {
        return $user->can('point.restore');
    }

    public function forceDelete(User $user, Point $model): bool
    {
        return $user->can('point.forceDelete');
    }
}
