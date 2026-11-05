<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Permissions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AuthorizePermission
{
    public function __construct(private readonly Permissions $permissions) {}

    public function handle(Request $request, Closure $next): Response
    {
        $route = (string) $request->route()?->getName();

        if (! $this->permissions->isPublicRoute($route)) {
            $permission = $this->permissions->forRoute($route);

            abort_unless($permission !== null && $request->user()?->hasPermission($permission), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
