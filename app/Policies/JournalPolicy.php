<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Journal;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('journal.index');
    }

    public function view(User $user, Journal $model): bool
    {
        return $user->can('journal.index');
    }

    public function create(User $user): bool
    {
        return $user->can('journal.create');
    }

    public function update(User $user, Journal $model): bool
    {
        return $user->can('journal.edit');
    }

    public function delete(User $user, Journal $model): bool
    {
        return $user->can('journal.destroy');
    }

    public function restore(User $user, Journal $model): bool
    {
        return $user->can('journal.restore');
    }

    public function forceDelete(User $user, Journal $model): bool
    {
        return $user->can('journal.forceDelete');
    }
}
