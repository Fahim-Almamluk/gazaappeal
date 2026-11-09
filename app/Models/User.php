<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Support\Permissions;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

#[Fillable(['name', 'email', 'password', 'role_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
final class User extends Authenticatable
{
    use HasFactory;
    use LogsActivity;
    use Notifiable;

    protected $attributes = [
        'is_super_admin' => false,
    ];

    /**
     * @var list<string>|null
     */
    private ?array $resolvedPermissions = null;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin || (bool) $this->role?->is_system;
    }

    /**
     * @return array<string, bool>
     */
    public function permissionOverrides(): array
    {
        return DB::table('permission_user')
            ->where('user_id', $this->getKey())
            ->pluck('granted', 'permission')
            ->map(fn (mixed $granted): bool => (bool) $granted)
            ->all();
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        if ($this->resolvedPermissions !== null) {
            return $this->resolvedPermissions;
        }

        $catalog = app(Permissions::class);

        if ($this->isSuperAdmin()) {
            return $this->resolvedPermissions = $catalog->all();
        }

        $overrides = $this->permissionOverrides();
        $granted = array_keys(array_filter($overrides));
        $revoked = array_keys(array_filter($overrides, fn (bool $granted): bool => ! $granted));

        return $this->resolvedPermissions = $catalog->only(array_diff([...($this->role?->permissions() ?? []), ...$granted], $revoked));
    }

    public function canManageRole(Role $role): bool
    {
        if ($role->is_system) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        $own = $this->permissions();
        $granted = $role->permissions();

        return array_diff($granted, $own) === [] && count($granted) < count($own);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * @param  iterable<string>  $permissions
     */
    public function syncPermissions(iterable $permissions): void
    {
        $permissions = app(Permissions::class)->only($permissions);
        $inherited = $this->role?->permissions() ?? [];
        $rows = [
            ...array_map(fn (string $permission): array => ['user_id' => $this->getKey(), 'permission' => $permission, 'granted' => true], array_values(array_diff($permissions, $inherited))),
            ...array_map(fn (string $permission): array => ['user_id' => $this->getKey(), 'permission' => $permission, 'granted' => false], array_values(array_diff($inherited, $permissions))),
        ];

        DB::transaction(function () use ($rows): void {
            DB::table('permission_user')->where('user_id', $this->getKey())->delete();
            DB::table('permission_user')->insert($rows);
        });

        $this->resolvedPermissions = null;
    }

    public function initial(): string
    {
        return mb_substr(trim($this->name), 0, 1);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
