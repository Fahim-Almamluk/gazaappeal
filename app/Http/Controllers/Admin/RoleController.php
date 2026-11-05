<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class RoleController extends Controller
{
    public function __construct(private readonly Permissions $permissions) {}

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->withCount('users')->orderByDesc('is_system')->orderBy('id')->get(),
            'total' => count($this->permissions->all()),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.form', ['role' => new Role]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $role = Role::query()->create($request->safe()->only('name'));
            $role->syncPermissions($request->validated('permissions', []));
        });

        return to_route('admin.roles.index')->with('success', __('admin.roles.created'));
    }

    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        return view('admin.roles.form', compact('role'));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        DB::transaction(function () use ($request, $role): void {
            $role->update($request->safe()->only('name'));
            $role->syncPermissions($request->validated('permissions', []));
        });

        return to_route('admin.roles.index')->with('success', __('admin.roles.updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        Gate::authorize('delete', $role);

        $role->delete();

        return to_route('admin.roles.index')->with('success', __('admin.roles.deleted'));
    }
}
