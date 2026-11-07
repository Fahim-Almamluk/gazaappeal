<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => self::$password ??= Hash::make('Secret-Pass-2026'),
            'role_id' => Role::factory()->editor(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role_id' => Role::factory()->system()]);
    }

    public function superAdmin(): static
    {
        return $this->admin()->afterMaking(fn (User $user) => $user->forceFill(['is_super_admin' => true]));
    }

    public function viewer(): static
    {
        return $this->state(['role_id' => Role::factory()->viewer()]);
    }

    /**
     * @param  list<string>  $permissions
     */
    public function withPermissions(array $permissions): static
    {
        return $this->state(['role_id' => Role::factory()->withPermissions($permissions)]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
