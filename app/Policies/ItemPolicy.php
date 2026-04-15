<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Item;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('item.index');
    }

    public function view(User $user, Item $model): bool
    {
        return $user->can('item.index');
    }

    public function create(User $user): bool
    {
        return $user->can('item.create');
    }

    public function update(User $user, Item $model): bool
    {
        return $user->can('item.edit');
    }

    public function delete(User $user, Item $model): bool
    {
        return $user->can('item.destroy');
    }

    public function restore(User $user, Item $model): bool
    {
        return $user->can('item.restore');
    }

    public function forceDelete(User $user, Item $model): bool
    {
        return $user->can('item.forceDelete');
    }
}
