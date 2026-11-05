<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

final class RolePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->hasPermission('roles.view');
    }

    public function create(User $actor): bool
    {
        return $actor->hasPermission('roles.create');
    }

    public function update(User $actor, Role $role): bool
    {
        return $actor->hasPermission('roles.update') && ! $role->is_system && $actor->role_id !== $role->getKey();
    }

    public function delete(User $actor, Role $role): bool
    {
        return $actor->hasPermission('roles.delete') && ! $role->is_system && $role->users()->doesntExist();
    }
}
