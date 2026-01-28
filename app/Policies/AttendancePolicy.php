<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('attendance.index');
    }

    public function view(User $user, Attendance $model): bool
    {
        return $user->can('attendance.index');
    }

    public function create(User $user): bool
    {
        return $user->can('attendance.create');
    }

    public function update(User $user, Attendance $model): bool
    {
        return $user->can('attendance.edit');
    }

    public function delete(User $user, Attendance $model): bool
    {
        return $user->can('attendance.destroy');
    }

    public function restore(User $user, Attendance $model): bool
    {
        return $user->can('attendance.restore');
    }

    public function forceDelete(User $user, Attendance $model): bool
    {
        return $user->can('attendance.forceDelete');
    }
}
