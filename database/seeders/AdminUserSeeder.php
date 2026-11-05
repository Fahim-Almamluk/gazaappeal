<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('appeal.admin');

        if (blank($admin['email']) || blank($admin['password'])) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        User::query()->firstOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'] ?: $admin['email'],
                'password' => $admin['password'],
                'role_id' => Role::query()->where('is_system', true)->value('id'),
                'is_active' => true,
            ],
        );
    }
}
