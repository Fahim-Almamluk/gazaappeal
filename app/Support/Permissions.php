<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

final class Permissions
{
    /**
     * @return list<string>
     */
    public function all(): array
    {
        return collect(config('permissions.tree'))
            ->flatMap(fn (array $group): array => collect($group['modules'])
                ->flatMap(fn (array $module, string $key): array => array_map(fn (string $action): string => "{$key}.{$action}", $module['actions']))
                ->all())
            ->values()
            ->all();
    }

    public function exists(string $permission): bool
    {
        return in_array($permission, $this->all(), true);
    }

    /**
     * @param  iterable<string>  $permissions
     * @return list<string>
     */
    public function only(iterable $permissions): array
    {
        return array_values(array_intersect($this->all(), [...$permissions]));
    }

    /**
     * @return list<array{key: string, icon: string, label: string, modules: list<array{key: string, icon: string, label: string, actions: list<array{key: string, label: string}>}>}>
     */
    public function tree(): array
    {
        return collect(config('permissions.tree'))
            ->map(fn (array $group, string $groupKey): array => [
                'key' => $groupKey,
                'icon' => $group['icon'],
                'label' => __("permissions.groups.{$groupKey}"),
                'modules' => collect($group['modules'])
                    ->map(fn (array $module, string $moduleKey): array => [
                        'key' => $moduleKey,
                        'icon' => $module['icon'],
                        'label' => __("permissions.modules.{$moduleKey}"),
                        'actions' => array_map(fn (string $action): array => [
                            'key' => "{$moduleKey}.{$action}",
                            'label' => $this->actionLabel($moduleKey, $action),
                        ], $module['actions']),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    public function forRoute(string $routeName): ?string
    {
        $segments = explode('.', Str::after($routeName, 'admin.'));
        $module = config('permissions.route_modules.'.$segments[0], $segments[0]);
        $action = config('permissions.route_actions.'.end($segments));

        return $action === null ? null : "{$module}.{$action}";
    }

    public function isPublicRoute(string $routeName): bool
    {
        return in_array($routeName, config('permissions.public_routes'), true);
    }

    public function isSuperAdminRoute(string $routeName): bool
    {
        return Str::startsWith($routeName, config('permissions.super_admin_routes'));
    }

    private function actionLabel(string $module, string $action): string
    {
        $override = "permissions.overrides.{$module}.{$action}";

        return trans()->has($override) ? __($override) : __("permissions.actions.{$action}");
    }
}
