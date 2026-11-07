<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Impersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

final class ImpersonationController extends Controller
{
    public function __construct(private readonly Impersonation $impersonation) {}

    public function store(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('impersonate', $user);
        abort_if($this->impersonation->active(), 403);

        $this->impersonation->start($request->user(), $user);

        return to_route('admin.dashboard')->with('success', __('admin.impersonation.started', ['name' => $user->name]));
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort_unless($this->impersonation->active(), 403);

        if ($this->impersonation->stop() === null) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return to_route('login');
        }

        return to_route('admin.users.index')->with('success', __('admin.impersonation.stopped'));
    }
}
