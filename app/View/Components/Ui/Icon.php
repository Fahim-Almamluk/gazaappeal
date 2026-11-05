<?php

declare(strict_types=1);

namespace App\View\Components\Ui;

use Illuminate\View\Component;
use InvalidArgumentException;

final class Icon extends Component
{
    private const array DIRECTIONAL = ['arrow-left', 'arrow-right', 'chevron-left', 'chevron-right', 'panel-right'];

    private static array $cache = [];

    public function __construct(
        public string $name,
        public int $size = 18,
    ) {}

    public function render(): string
    {
        return '<svg {{ $attributes->merge(["width" => $size, "height" => $size, "aria-hidden" => "true", "focusable" => "false", "class" => $classes()]) }} xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">'
            .$this->paths()
            .'</svg>';
    }

    public function classes(): string
    {
        return in_array($this->name, self::DIRECTIONAL, true) ? 'shrink-0 ltr:-scale-x-100' : 'shrink-0';
    }

    public static function markup(string $name): string
    {
        return self::$cache[$name] ??= self::load($name);
    }

    private function paths(): string
    {
        return self::markup($this->name);
    }

    private static function load(string $name): string
    {
        $path = resource_path("svg/icons/{$name}.svg");

        if (! preg_match('/^[a-z0-9-]+$/', $name) || ! is_file($path)) {
            throw new InvalidArgumentException("Unknown icon [{$name}].");
        }

        preg_match('/<svg[^>]*>(.*)<\/svg>/s', (string) file_get_contents($path), $matches);

        return trim($matches[1] ?? '');
    }
}
