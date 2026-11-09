<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        $response->headers->add([
            'Content-Security-Policy' => $this->contentSecurityPolicy(Vite::cspNonce()),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ]);

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->remove('X-Powered-By');

        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }

        return $response;
    }

    private function contentSecurityPolicy(string $nonce): string
    {
        $hot = Vite::isRunningHot() ? $this->hotOrigin() : null;

        $directives = [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'nonce-{$nonce}'", $hot['http'] ?? null],
            'style-src' => ["'self'", $hot ? "'unsafe-inline'" : "'nonce-{$nonce}'", $hot['http'] ?? null],
            'img-src' => ["'self'", 'data:', 'blob:'],
            'font-src' => ["'self'", $hot['http'] ?? null],
            'connect-src' => ["'self'", $hot['http'] ?? null, $hot['ws'] ?? null],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
        ];

        return collect($directives)
            ->map(fn (array $sources, string $name): string => $name.' '.implode(' ', array_filter($sources)))
            ->implode('; ');
    }

    private function hotOrigin(): array
    {
        $http = rtrim((string) file_get_contents(public_path('hot')));

        return ['http' => $http, 'ws' => preg_replace('/^http/', 'ws', $http)];
    }
}
