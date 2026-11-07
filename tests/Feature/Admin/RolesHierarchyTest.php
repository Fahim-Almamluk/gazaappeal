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

    public function test_a_non_super_admin_can_only_assign_strictly_lower_roles(): void
    {
        $lower = Role::factory()->withPermissions(['users.view'])->create();
        $same = Role::factory()->withPermissions(['users.view', 'users.create', 'faqs.view'])->create();
        $higher = Role::factory()->withPermissions(['users.view', 'users.create', 'faqs.view', 'faqs.create'])->create();
        $actor = User::factory()->create(['role_id' => Role::factory()->withPermissions(['users.view', 'users.create', 'faqs.view'])]);

        $ids = Role::assignableBy($actor)->modelKeys();

        $this->assertContains($lower->id, $ids);
        $this->assertNotContains($same->id, $ids);
        $this->assertNotContains($higher->id, $ids);
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

    private function newUserData(int $roleId, string $email): array
    {
        return [
            'name' => 'مستخدم جديد',
            'email' => $email,
            'role_id' => $roleId,
            'password' => 'Abcdefg1',
            'password_confirmation' => 'Abcdefg1',
        ];
    }

    public function test_admin_cannot_create_another_admin_or_a_super_admin(): void
    {
        $adminRole = Role::factory()->administrator()->create();
        $systemRole = Role::query()->where('is_system', true)->sole();
        $this->actingAs(User::factory()->create(['role_id' => $adminRole]));

        $this->post(route('admin.users.store'), $this->newUserData($adminRole->id, 'peer@example.test'))->assertSessionHasErrors('role_id');
        $this->post(route('admin.users.store'), $this->newUserData($systemRole->id, 'super@example.test'))->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'peer@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'super@example.test']);
    }

    public function test_admin_can_create_lower_level_users(): void
    {
        $this->actingAs(User::factory()->administrator()->create());
        $editor = Role::factory()->editor()->create();

        $this->post(route('admin.users.store'), $this->newUserData($editor->id, 'editor@example.test'))->assertSessionHasNoErrors();

        $this->assertSame($editor->id, User::query()->where('email', 'editor@example.test')->value('role_id'));
    }

    public function test_only_the_super_admin_can_create_an_admin(): void
    {
        $adminRole = Role::factory()->administrator()->create();
        $this->actingAs(User::factory()->superAdmin()->create());

        $this->post(route('admin.users.store'), $this->newUserData($adminRole->id, 'admin2@example.test'))->assertSessionHasNoErrors();

        $created = User::query()->where('email', 'admin2@example.test')->firstOrFail();
        $this->assertSame($adminRole->id, $created->role_id);
        $this->assertFalse($created->isSuperAdmin());
    }

    public function test_admin_cannot_manage_another_admin_but_can_manage_lower_users(): void
    {
        $admin = User::factory()->administrator()->create();
        $peer = User::factory()->administrator()->create();
        $editor = User::factory()->create();
        $this->actingAs($admin);

        $this->get(route('admin.users.edit', $peer))->assertForbidden();
        $this->patch(route('admin.users.toggle', $peer))->assertForbidden();
        $this->get(route('admin.users.permissions', $peer))->assertForbidden();
        $this->get(route('admin.users.edit', $editor))->assertOk();
        $this->get(route('admin.users.edit', $admin))->assertOk();
    }

    public function test_restricted_admin_cannot_create_an_admin_with_more_permissions_than_their_own(): void
    {
        $adminRole = Role::factory()->administrator()->create();
        $restricted = Role::factory()->withPermissions(['users.view', 'users.create'])->create();
        $actor = User::factory()->create(['role_id' => $restricted]);

        $this->actingAs($actor)->post(route('admin.users.store'), $this->newUserData($adminRole->id, 'escalated@example.test'))->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('users', ['email' => 'escalated@example.test']);
    }
}
