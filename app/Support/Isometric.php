<?php

declare(strict_types=1);

namespace App\Support;

final class Isometric
{
    private const float COS = 0.866;

    private const float SIN = 0.5;

    public function __construct(
        private readonly float $originX = 300,
        private readonly float $originY = 280,
    ) {}

    public function point(float $x, float $y, float $z = 0): array
    {
        return [
            round($this->originX + ($x - $y) * self::COS, 1),
            round($this->originY + ($x + $y) * self::SIN - $z, 1),
        ];
    }

    public function polygon(array ...$corners): string
    {
        return collect($corners)
            ->map(fn (array $corner): string => implode(',', $this->point(...$corner)))
            ->implode(' ');
    }

    public function path(array ...$corners): string
    {
        return 'M'.collect($corners)
            ->map(fn (array $corner): string => implode(' ', $this->point(...$corner)))
            ->implode('L');
    }

    public function box(float $x, float $y, float $width, float $depth, float $height, float $base = 0): array
    {
        $top = $base + $height;

        return [
            'top' => $this->polygon([$x, $y, $top], [$x + $width, $y, $top], [$x + $width, $y + $depth, $top], [$x, $y + $depth, $top]),
            'right' => $this->polygon([$x + $width, $y, $base], [$x + $width, $y + $depth, $base], [$x + $width, $y + $depth, $top], [$x + $width, $y, $top]),
            'left' => $this->polygon([$x, $y + $depth, $base], [$x + $width, $y + $depth, $base], [$x + $width, $y + $depth, $top], [$x, $y + $depth, $top]),
        ];
    }

    /**
     * @param  array<int, float>  $heights
     */
    public function wall(string $plane, float $fixed, float $from, float $to, array $heights): string
    {
        $steps = count($heights) - 1;
        $at = fn (int $step): float => $from + ($to - $from) * $step / $steps;
        $corner = fn (float $along, float $z): array => $plane === 'x' ? [$fixed, $along, $z] : [$along, $fixed, $z];

        $corners = [$corner($from, 0), $corner($to, 0)];

        foreach (array_reverse(array_keys($heights)) as $step) {
            $corners[] = $corner($at($step), $heights[$step]);
        }

        return $this->polygon(...$corners);
    }

    public function tent(float $x, float $y, float $width, float $depth, float $height): array
    {
        $ridge = $y + $depth / 2;

        return [
            'slope' => $this->polygon([$x, $y + $depth, 0], [$x + $width, $y + $depth, 0], [$x + $width, $ridge, $height], [$x, $ridge, $height]),
            'gable' => $this->polygon([$x + $width, $y, 0], [$x + $width, $y + $depth, 0], [$x + $width, $ridge, $height]),
            'lantern' => $this->point($x + $width, $ridge, $height * 0.3),
        ];
    }

    public function platform(float $half, float $thickness): array
    {
        return [
            'top' => $this->polygon([-$half, -$half, 0], [$half, -$half, 0], [$half, $half, 0], [-$half, $half, 0]),
            'right' => $this->polygon([$half, -$half, 0], [$half, $half, 0], [$half, $half, -$thickness], [$half, -$half, -$thickness]),
            'left' => $this->polygon([-$half, $half, 0], [$half, $half, 0], [$half, $half, -$thickness], [-$half, $half, -$thickness]),
            'grid' => collect(range(-$half + 50, $half - 50, 50))
                ->flatMap(fn (float $at): array => [
                    $this->path([$at, -$half, 0], [$at, $half, 0]),
                    $this->path([-$half, $at, 0], [$half, $at, 0]),
                ])
                ->implode(''),
        ];
    }
}
