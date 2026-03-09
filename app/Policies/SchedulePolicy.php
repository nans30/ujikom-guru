<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('schedule.index');
    }

    public function view(User $user, Schedule $model): bool
    {
        return $user->can('schedule.index');
    }

    public function create(User $user): bool
    {
        return $user->can('schedule.create');
    }

    public function update(User $user, Schedule $model): bool
    {
        return $user->can('schedule.edit');
    }

    public function delete(User $user, Schedule $model): bool
    {
        return $user->can('schedule.destroy');
    }

    public function restore(User $user, Schedule $model): bool
    {
        return $user->can('schedule.restore');
    }

    public function forceDelete(User $user, Schedule $model): bool
    {
        return $user->can('schedule.forceDelete');
    }
}
