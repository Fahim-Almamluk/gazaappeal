<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

final class Impersonation
{
    private const string KEY = 'impersonator_id';

    public function __construct(private readonly Session $session) {}

    public function start(User $impersonator, User $user): void
    {
        Auth::guard('web')->login($user);
        $this->session->put(self::KEY, $impersonator->getKey());
        $this->session->regenerateToken();
    }

    public function stop(): ?User
    {
        $impersonator = User::query()->find($this->session->pull(self::KEY));

        if (! $impersonator?->is_super_admin || ! $impersonator->is_active) {
            return null;
        }

        Auth::guard('web')->login($impersonator);
        $this->session->regenerateToken();

        return $impersonator;
    }

    public function impersonatorId(): ?int
    {
        $id = $this->session->get(self::KEY);

        return $id === null ? null : (int) $id;
    }

    public function active(): bool
    {
        return $this->session->has(self::KEY);
    }
}
