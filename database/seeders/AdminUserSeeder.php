<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('appeal.admin');

        if (blank($admin['email']) || blank($admin['password'])) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        DB::transaction(function () use ($admin): void {
            $user = User::query()->firstOrNew(['email' => $admin['email']]);

            if (! $user->exists) {
                $user->fill([
                    'name' => $admin['name'] ?: $admin['email'],
                    'password' => $admin['password'],
                    'role_id' => Role::query()->where('is_system', true)->value('id'),
                ]);
            }

            $user->forceFill(['is_active' => true, 'is_super_admin' => true])->save();

            User::query()->whereKeyNot($user->getKey())->where('is_super_admin', true)->update(['is_super_admin' => false]);
        });
    }
}
