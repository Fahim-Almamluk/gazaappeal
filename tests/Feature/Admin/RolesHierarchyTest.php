<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Support\Permissions;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RolesHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_a_fixed_super_admin_role_and_an_editable_admin_role(): void
    {
        $existing = Role::query()->where('is_system', true)->sole();
        $existing->forceFill(['name' => json_encode(['ar' => 'مدير النظام'], JSON_UNESCAPED_UNICODE)])->save();

        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class);

        $superAdmin = Role::query()->where('is_system', true)->sole();
        $this->assertSame($existing->id, $superAdmin->id);
        $this->assertSame(['ar' => 'سوبر أدمن', 'en' => 'Super admin'], $superAdmin->translations('name'));

        $admin = Role::query()->whereTranslation('name', 'أدمن')->sole();
        $this->assertFalse($admin->is_system);
        $this->assertCount(count(app(Permissions::class)->all()), $admin->permissions());
        $this->assertSame(1, Role::query()->whereTranslation('name', 'أدمن')->count());
    }

    public function test_super_admin_always_has_every_permission_whatever_the_role(): void
    {
        $owner = User::factory()->superAdmin()->create(['role_id' => Role::factory()->viewer()]);

        $this->assertEqualsCanonicalizing(app(Permissions::class)->all(), $owner->permissions());
        $this->actingAs($owner)->get(route('admin.roles.create'))->assertOk();
    }

    public function test_admin_role_is_limited_to_its_configured_permissions(): void
    {
        $role = Role::factory()->administrator()->create();
        $role->syncPermissions(array_values(array_diff($role->permissions(), ['settings.update'])));
        $admin = User::factory()->create(['role_id' => $role]);

        $this->assertFalse($admin->isSuperAdmin());
        $this->actingAs($admin)->get(route('admin.settings.edit'))->assertForbidden();
        $this->get(route('admin.users.index'))->assertOk();
    }

    public function test_admin_cannot_impersonate_or_touch_the_super_admin(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $admin = User::factory()->administrator()->create();
        $editor = User::factory()->create();
        $this->actingAs($admin);

        $this->post(route('admin.users.impersonate', $editor))->assertForbidden();
        $this->get(route('admin.users.edit', $owner))->assertForbidden();
        $this->patch(route('admin.users.toggle', $owner))->assertForbidden();
        $this->get(route('admin.users.permissions', $owner))->assertForbidden();
    }

    public function test_the_super_admin_role_is_never_assignable_from_the_dashboard(): void
    {
        $superAdminRole = Role::query()->where('is_system', true)->sole();
        $adminRole = Role::factory()->administrator()->create();

        $owner = User::factory()->superAdmin()->create(['role_id' => $superAdminRole]);
        $assignable = Role::assignableBy($owner);

        $this->assertContains($adminRole->id, $assignable->modelKeys());
        $this->assertNotContains($superAdminRole->id, $assignable->modelKeys());
        $this->assertFalse($assignable->contains('is_system', true));

        $this->actingAs($owner)->post(route('admin.users.store'), [
            'name' => 'مستخدم',
            'email' => 'new@example.test',
            'role_id' => $superAdminRole->id,
            'password' => 'Abcdefg1',
            'password_confirmation' => 'Abcdefg1',
        ])->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'new@example.test']);
    }

    public function test_an_admin_can_only_assign_roles_within_their_own_permissions(): void
    {
        $limited = Role::factory()->withPermissions(['users.view', 'users.create'])->create(['name' => ['ar' => 'محدود']]);
        $viewer = Role::factory()->withPermissions(['users.view'])->create();
        $admin = User::factory()->create(['role_id' => Role::factory()->withPermissions(['users.view', 'users.create'])]);

        $ids = Role::assignableBy($admin)->modelKeys();

        $this->assertContains($limited->id, $ids);
        $this->assertContains($viewer->id, $ids);
    }

    public function test_roles_screen_lists_both_roles_with_the_system_one_locked(): void
    {
        $this->seed(RoleSeeder::class);
        $owner = User::factory()->superAdmin()->create(['role_id' => Role::query()->where('is_system', true)->value('id')]);

        $this->actingAs($owner)->withSession(['locale' => 'ar'])->get(route('admin.roles.index'))
            ->assertOk()
            ->assertSee('سوبر أدمن')
            ->assertSee('أدمن')
            ->assertSee('دور النظام');

        $systemRole = Role::query()->where('is_system', true)->sole();
        $this->put(route('admin.roles.update', $systemRole), ['name' => ['ar' => 'x'], 'permissions' => []])->assertForbidden();
        $this->delete(route('admin.roles.destroy', $systemRole))->assertForbidden();
    }

    public function test_admin_can_create_another_admin_but_never_a_super_admin(): void
    {
        $adminRole = Role::factory()->administrator()->create();
        $admin = User::factory()->create(['role_id' => $adminRole]);
        $this->actingAs($admin);

        $this->post(route('admin.users.store'), [
            'name' => 'أدمن ثاني',
            'email' => 'second@example.test',
            'role_id' => $adminRole->id,
            'password' => 'Abcdefg1',
            'password_confirmation' => 'Abcdefg1',
        ])->assertSessionHasNoErrors();

        $second = User::query()->where('email', 'second@example.test')->firstOrFail();
        $this->assertSame($adminRole->id, $second->role_id);
        $this->assertFalse($second->isSuperAdmin());
    }

    public function test_restricted_admin_cannot_create_an_admin_with_more_permissions_than_their_own(): void
    {
        $adminRole = Role::factory()->administrator()->create();
        $restricted = Role::factory()->withPermissions(['users.view', 'users.create'])->create();
        $actor = User::factory()->create(['role_id' => $restricted]);

        $this->actingAs($actor)->post(route('admin.users.store'), [
            'name' => 'أدمن',
            'email' => 'escalated@example.test',
            'role_id' => $adminRole->id,
            'password' => 'Abcdefg1',
            'password_confirmation' => 'Abcdefg1',
        ])->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'escalated@example.test']);
    }
}
