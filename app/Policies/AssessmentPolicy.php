<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Assessment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('assessment.index');
    }

    public function view(User $user, Assessment $model): bool
    {
        return $user->can('assessment.index');
    }

    public function create(User $user): bool
    {
        return $user->can('assessment.create');
    }

    public function update(User $user, Assessment $model): bool
    {
        return $user->can('assessment.edit');
    }

    public function delete(User $user, Assessment $model): bool
    {
        return $user->can('assessment.destroy');
    }

    public function restore(User $user, Assessment $model): bool
    {
        return $user->can('assessment.restore');
    }

    public function forceDelete(User $user, Assessment $model): bool
    {
        return $user->can('assessment.forceDelete');
    }
}
