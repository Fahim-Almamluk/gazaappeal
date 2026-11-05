<?php

namespace Database\Factories;

use App\Models\Role;
use App\Support\Permissions;
use Illuminate\Database\Eloquent\Factories\Factory;

final class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => ['ar' => 'دور '.fake()->unique()->numberBetween(1, 99999), 'en' => 'Role '.fake()->unique()->numberBetween(1, 99999)],
        ];
    }

    public function system(): static
    {
        return $this->state(['name' => ['ar' => 'مدير النظام', 'en' => 'System administrator']])
            ->afterMaking(fn (Role $role) => $role->forceFill(['is_system' => true]));
    }

    /**
     * @param  list<string>  $permissions
     */
    public function withPermissions(array $permissions): static
    {
        return $this->afterCreating(fn (Role $role) => $role->syncPermissions($permissions));
    }

    public function editor(): static
    {
        $modules = ['projects', 'losses', 'damages', 'stories', 'gallery', 'partners', 'faqs', 'donation_methods'];

        return $this->withPermissions([
            ...collect($modules)->crossJoin(['view', 'create', 'update', 'delete'])->map(fn (array $pair): string => implode('.', $pair))->all(),
            'volunteers.view', 'volunteers.update', 'messages.view', 'messages.update',
        ]);
    }

    public function viewer(): static
    {
        return $this->withPermissions(array_values(array_filter(
            app(Permissions::class)->all(),
            fn (string $permission): bool => str_ends_with($permission, '.view') && ! in_array(explode('.', $permission)[0], ['users', 'roles'], true),
        )));
    }
}
