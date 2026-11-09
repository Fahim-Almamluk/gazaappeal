<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserPermissionsRequest;
use App\Models\User;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class UserPermissionController extends Controller
{
    public function __construct(private readonly Permissions $permissions) {}

    public function edit(User $user): View
    {
        Gate::authorize('permissions', $user);

        $overrides = $user->permissionOverrides();

        return view('admin.users.permissions', [
            'user' => $user->load('role'),
            'inherited' => $user->role?->permissions() ?? [],
            'granted' => count(array_filter($overrides)),
            'revoked' => count($overrides) - count(array_filter($overrides)),
        ]);
    }

    public function update(UserPermissionsRequest $request, User $user): RedirectResponse
    {
        $user->syncPermissions($this->permissions->withLockedKept($request->user(), $request->validated('permissions', []), $user->permissions()));

        return to_route('admin.users.permissions', $user)->with('success', __('admin.users.permissions.saved'));
    }
}
