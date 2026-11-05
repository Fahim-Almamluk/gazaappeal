<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Localization;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function __construct(private readonly Localization $localization) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        App::setLocale($locale);
        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        $request->route()?->forgetParameter('locale');

        return $next($request);
    }

    private function resolve(Request $request): string
    {
        $candidates = [
            $request->route('locale'),
            $request->hasSession() ? $request->session()->get('locale') : null,
            $request->getPreferredLanguage($this->localization->supported()),
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $this->localization->isSupported($candidate)) {
                return $candidate;
            }
        }

        return $this->localization->default();
    }
}
