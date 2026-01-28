<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('teacher.index');
    }

    public function view(User $user, Teacher $model): bool
    {
        return $user->can('teacher.index');
    }

    public function create(User $user): bool
    {
        return $user->can('teacher.create');
    }

    public function update(User $user, Teacher $model): bool
    {
        return $user->can('teacher.edit');
    }

    public function delete(User $user, Teacher $model): bool
    {
        return $user->can('teacher.destroy');
    }

    public function restore(User $user, Teacher $model): bool
    {
        return $user->can('teacher.restore');
    }

    public function forceDelete(User $user, Teacher $model): bool
    {
        return $user->can('teacher.forceDelete');
    }
}
