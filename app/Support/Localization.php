<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\Request;

final class Localization
{
    public function __construct(private readonly Request $request) {}

    /**
     * @return list<string>
     */
    public function supported(): array
    {
        return array_keys(config('appeal.locales'));
    }

    public function isSupported(?string $locale): bool
    {
        return $locale !== null && in_array($locale, $this->supported(), true);
    }

    public function default(): string
    {
        return config('app.locale');
    }

    public function source(): string
    {
        return config('app.fallback_locale');
    }

    public function current(): string
    {
        return app()->getLocale();
    }

    public function direction(?string $locale = null): string
    {
        return config('appeal.locales.'.($locale ?? $this->current()).'.dir', 'rtl');
    }

    public function ogLocale(?string $locale = null): string
    {
        return config('appeal.locales.'.($locale ?? $this->current()).'.og');
    }

    public function alternate(): string
    {
        return collect($this->supported())->first(fn (string $locale): bool => $locale !== $this->current());
    }

    public function isLocalizedPage(): bool
    {
        $route = $this->request->route();

        return $route !== null && $route->getName() !== null && in_array('locale', $route->parameterNames(), true);
    }

    public function urlFor(string $locale): string
    {
        if (! $this->isLocalizedPage()) {
            return route('locale.switch', ['locale' => $locale]);
        }

        $route = $this->request->route();

        return route($route->getName(), [...$this->request->query(), ...$route->parameters(), 'locale' => $locale]);
    }

    /**
     * @return array<string, string>
     */
    public function alternates(): array
    {
        if (! $this->isLocalizedPage()) {
            return [];
        }

        return collect($this->supported())
            ->mapWithKeys(fn (string $locale): array => [$locale => $this->urlFor($locale)])
            ->all();
    }
}
