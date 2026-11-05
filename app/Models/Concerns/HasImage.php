<?php

declare(strict_types=1);

namespace App\Models\Concerns;

trait HasImage
{
    public function imageUrl(): ?string
    {
        $path = $this->{$this->imageColumn()};

        return $path ? asset('storage/'.$path) : null;
    }

    public function imageColumn(): string
    {
        return 'image';
    }
}
