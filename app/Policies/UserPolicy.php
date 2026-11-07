<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->hasPermission('users.view');
    }

    public function create(User $actor): bool
    {
        return $actor->hasPermission('users.create');
    }

    public function update(User $actor, User $user): bool
    {
        if ($user->is_super_admin) {
            return $actor->is($user);
        }

        return $actor->hasPermission('users.update') && ($actor->isAdmin() || ! $user->isAdmin());
    }

    public function toggle(User $actor, User $user): bool
    {
        return $this->update($actor, $user) && $actor->isNot($user);
    }

    public function permissions(User $actor, User $user): bool
    {
        return $this->update($actor, $user) && $actor->isNot($user) && ! $user->isAdmin();
    }

    public function impersonate(User $actor, User $user): bool
    {
        return $actor->is_super_admin && $actor->isNot($user) && ! $user->is_super_admin && $user->is_active;
    }
}
