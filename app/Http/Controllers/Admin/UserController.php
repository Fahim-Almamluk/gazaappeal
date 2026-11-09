<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class UserController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->string('q')->trim()->toString();
        $role = $request->integer('role') ?: null;
        $status = in_array($request->query('status'), ['active', 'inactive'], true) ? $request->query('status') : null;

        $users = User::query()
            ->with('role')
            ->when($role, fn ($query, int $roleId) => $query->where('role_id', $roleId))
            ->when($status, fn ($query, string $state) => $query->where('is_active', $state === 'active'))
            ->addSelect([
                'granted_count' => DB::table('permission_user')->selectRaw('count(*)')->whereColumn('user_id', 'users.id')->where('granted', true),
                'revoked_count' => DB::table('permission_user')->selectRaw('count(*)')->whereColumn('user_id', 'users.id')->where('granted', false),
            ])
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->orderByDesc('is_super_admin')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        $roles = Role::query()->orderByDesc('is_system')->orderBy('id')->get()->mapWithKeys(fn (Role $item): array => [$item->id => $item->name])->all();

        return view('admin.users.index', compact('users', 'search', 'role', 'status', 'roles'));
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.users.form', ['user' => new User(['is_active' => true])]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::query()->create([...$request->validated(), 'is_active' => true]);

        return to_route('admin.users.index')->with('success', __('admin.users.created'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('admin.users.form', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->fill($request->safe()->except('password'));

        $passwordChanged = $request->filled('password');

        if ($passwordChanged) {
            $user->password = $request->validated('password');
        }

        $user->save();

        if ($passwordChanged) {
            $user->revokeSessions();
        }

        return to_route('admin.users.index')->with('success', __('admin.users.updated'));
    }

    public function toggle(User $user): RedirectResponse
    {
        Gate::authorize('toggle', $user);

        DB::transaction(function () use ($user): void {
            $user->forceFill(['is_active' => ! $user->is_active])->save();

            if (! $user->is_active) {
                $user->revokeSessions();
            }
        });

        return to_route('admin.users.index')->with('success', __($user->is_active ? 'admin.users.activated' : 'admin.users.deactivated'));
    }
}
