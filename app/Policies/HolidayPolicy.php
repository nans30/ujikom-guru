<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Holiday;
use Illuminate\Auth\Access\HandlesAuthorization;

class HolidayPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('holiday.index');
    }

    public function view(User $user, Holiday $model): bool
    {
        return $user->can('holiday.index');
    }

    public function create(User $user): bool
    {
        return $user->can('holiday.create');
    }

    public function update(User $user, Holiday $model): bool
    {
        return $user->can('holiday.edit');
    }

    public function delete(User $user, Holiday $model): bool
    {
        return $user->can('holiday.destroy');
    }

    public function restore(User $user, Holiday $model): bool
    {
        return $user->can('holiday.restore');
    }

    public function forceDelete(User $user, Holiday $model): bool
    {
        return $user->can('holiday.forceDelete');
    }
}
