<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_makes_the_env_account_the_only_super_admin(): void
    {
        $this->seed(RoleSeeder::class);
        $previous = User::factory()->superAdmin()->create();
        config(['appeal.admin' => ['name' => 'المدير', 'email' => 'owner@example.com', 'password' => 'Owner-Pass-2026!']]);

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $owner = User::query()->where('email', 'owner@example.com')->sole();
        $this->assertTrue($owner->is_super_admin);
        $this->assertTrue($owner->role->is_system);
        $this->assertFalse($previous->fresh()->is_super_admin);
        $this->assertSame(1, User::query()->where('is_super_admin', true)->count());
    }

    public function test_super_admin_cannot_be_granted_from_the_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $editor = User::factory()->create();

        $this->post(route('admin.users.store'), [
            'name' => 'مستخدم جديد',
            'email' => 'new@example.com',
            'role_id' => Role::query()->where('is_system', true)->value('id'),
            'password' => 'Secret-Pass-2026!',
            'password_confirmation' => 'Secret-Pass-2026!',
            'is_super_admin' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $this->put(route('admin.users.update', $editor), [
            'name' => $editor->name,
            'email' => $editor->email,
            'role_id' => $editor->role_id,
            'is_super_admin' => '1',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame(0, User::query()->where('is_super_admin', true)->count());
    }

    public function test_only_the_super_admin_sees_the_sign_in_as_button(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->create();
        $inactive = User::factory()->inactive()->create();

        $this->actingAs($owner)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(route('admin.users.impersonate', $editor), false)
            ->assertSee(route('admin.users.impersonate', $admin), false)
            ->assertDontSee(route('admin.users.impersonate', $owner), false)
            ->assertDontSee(route('admin.users.impersonate', $inactive), false);

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertDontSee(route('admin.users.impersonate', $editor), false);
        $this->post(route('admin.users.impersonate', $editor))->assertForbidden();
        $this->assertAuthenticatedAs($admin);
    }

    public function test_super_admin_signs_in_as_a_user_and_returns(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $editor = User::factory()->create(['name' => 'محرر المحتوى']);

        $this->actingAs($owner)->get(route('admin.users.index'));
        $before = session()->getId();

        $this->post(route('admin.users.impersonate', $editor))->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($editor);
        $this->assertNotSame($before, session()->getId());
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.impersonation.leave'), false)
            ->assertSee('محرر المحتوى');
        $this->get(route('admin.users.index'))->assertForbidden();

        $during = session()->getId();
        $this->delete(route('admin.impersonation.leave'))->assertRedirect(route('admin.users.index'));

        $this->assertAuthenticatedAs($owner);
        $this->assertNotSame($during, session()->getId());
        $this->get(route('admin.dashboard'))->assertOk()->assertDontSee(route('admin.impersonation.leave'), false);
    }

    public function test_super_admin_and_inactive_accounts_cannot_be_impersonated(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $inactive = User::factory()->inactive()->create();
        $this->actingAs($owner);

        $this->post(route('admin.users.impersonate', $owner))->assertForbidden();
        $this->post(route('admin.users.impersonate', $inactive))->assertForbidden();
        $this->assertAuthenticatedAs($owner);
    }

    public function test_leaving_requires_an_active_impersonation(): void
    {
        $this->actingAs(User::factory()->create())->delete(route('admin.impersonation.leave'))->assertForbidden();
    }

    public function test_leaving_after_the_super_admin_was_deactivated_signs_out(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $editor = User::factory()->create();

        $this->actingAs($owner)->post(route('admin.users.impersonate', $editor));
        $owner->forceFill(['is_active' => false])->save();

        $this->delete(route('admin.impersonation.leave'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_other_admins_cannot_manage_the_super_admin(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('admin.users.edit', $owner))->assertForbidden();
        $this->patch(route('admin.users.toggle', $owner))->assertForbidden();
        $this->get(route('admin.users.permissions', $owner))->assertForbidden();
        $this->assertTrue($owner->fresh()->is_active);
    }

    public function test_super_admin_has_every_permission_whatever_the_role(): void
    {
        $owner = User::factory()->superAdmin()->create(['role_id' => Role::factory()->viewer()]);

        $this->actingAs($owner)->get(route('admin.lookups.create'))->assertOk();
        $this->assertTrue($owner->hasPermission('roles.delete'));
    }

    public function test_super_admin_manages_extra_permissions_shown_in_the_users_list(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $editor = User::factory()->create();
        $this->actingAs($owner)->withSession(['locale' => 'ar']);

        $this->get(route('admin.users.permissions', $editor))->assertOk();
        $this->put(route('admin.users.permissions.update', $editor), [
            'permissions' => [...array_diff($editor->permissions(), ['faqs.delete']), 'settings.update', 'lookups.view'],
        ])->assertRedirect(route('admin.users.permissions', $editor));

        $this->assertTrue($editor->fresh()->hasPermission('lookups.view'));
        $this->assertFalse($editor->fresh()->hasPermission('faqs.delete'));
        $this->get(route('admin.users.index'))
            ->assertSee('2 صلاحيات مضافة')
            ->assertSee('صلاحية محجوبة')
            ->assertSee(route('admin.users.permissions', $editor), false);
    }
}
