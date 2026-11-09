<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
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

    /**
     * @return list<string>
     */
    public function restricted(): array
    {
        return array_values(array_filter(
            $this->all(),
            fn (string $permission): bool => in_array(Str::before($permission, '.'), config('permissions.super_admin_modules'), true),
        ));
    }

    /**
     * @return list<string>
     */
    public function lockedFor(User $actor): array
    {
        return $actor->isSuperAdmin() ? [] : $this->restricted();
    }

    /**
     * @param  iterable<string>  $extra
     * @return list<string>
     */
    public function grantableBy(User $actor, iterable $extra = []): array
    {
        if ($actor->isSuperAdmin()) {
            return $this->all();
        }

        return array_values(array_diff([...$actor->permissions(), ...$extra], $this->restricted()));
    }

    /**
     * @param  iterable<string>  $submitted
     * @param  iterable<string>  $current
     * @return list<string>
     */
    public function withLockedKept(User $actor, iterable $submitted, iterable $current): array
    {
        $locked = $this->lockedFor($actor);

        return array_values(array_unique([...array_diff([...$submitted], $locked), ...array_intersect([...$current], $locked)]));
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
    public function tree(bool $withRestricted = true): array
    {
        $hidden = $withRestricted ? [] : config('permissions.super_admin_modules');

        return collect(config('permissions.tree'))
            ->map(fn (array $group, string $groupKey): array => [
                'key' => $groupKey,
                'icon' => $group['icon'],
                'label' => __("permissions.groups.{$groupKey}"),
                'modules' => collect($group['modules'])
                    ->except($hidden)
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

    private function actionLabel(string $module, string $action): string
    {
        $override = "permissions.overrides.{$module}.{$action}";

        return trans()->has($override) ? __($override) : __("permissions.actions.{$action}");
    }
}
