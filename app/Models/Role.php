<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\Permissions;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable(['name'])]
final class Role extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * @var list<string>
     */
    protected array $translatable = ['name'];

    /**
     * @var list<string>|null
     */
    private ?array $grantedPermissions = null;

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        if ($this->is_system) {
            return app(Permissions::class)->all();
        }

        return $this->grantedPermissions ??= app(Permissions::class)->only(
            DB::table('permission_role')->where('role_id', $this->getKey())->pluck('permission'),
        );
    }

    /**
     * @param  iterable<string>  $permissions
     */
    public function syncPermissions(iterable $permissions): void
    {
        $permissions = app(Permissions::class)->only($permissions);

        DB::transaction(function () use ($permissions): void {
            DB::table('permission_role')->where('role_id', $this->getKey())->delete();
            DB::table('permission_role')->insert(array_map(fn (string $permission): array => ['role_id' => $this->getKey(), 'permission' => $permission], $permissions));
        });

        $this->grantedPermissions = $permissions;
    }

    /**
     * @return Collection<int, self>
     */
    public static function assignableBy(User $actor): Collection
    {
        return self::query()
            ->orderByDesc('is_system')
            ->orderBy('id')
            ->get()
            ->filter(fn (self $role): bool => $actor->canManageRole($role))
            ->values();
    }

    public function permissionCount(): int
    {
        return count($this->permissions());
    }

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }
}
