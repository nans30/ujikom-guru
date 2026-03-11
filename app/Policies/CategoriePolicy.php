<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Categorie;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('categorie.index');
    }

    public function view(User $user, Categorie $model): bool
    {
        return $user->can('categorie.index');
    }

    public function create(User $user): bool
    {
        return $user->can('categorie.create');
    }

    public function update(User $user, Categorie $model): bool
    {
        return $user->can('categorie.edit');
    }

    public function delete(User $user, Categorie $model): bool
    {
        return $user->can('categorie.destroy');
    }

    public function restore(User $user, Categorie $model): bool
    {
        return $user->can('categorie.restore');
    }

    public function forceDelete(User $user, Categorie $model): bool
    {
        return $user->can('categorie.forceDelete');
    }
}
