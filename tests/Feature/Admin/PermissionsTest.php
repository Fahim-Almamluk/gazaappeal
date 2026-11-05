<?php

namespace Tests\Feature\Admin;

use App\Models\Faq;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_viewer_can_browse_but_not_change_content(): void
    {
        $faq = Faq::factory()->create();
        $this->actingAs(User::factory()->viewer()->create());

        $this->get(route('admin.faqs.index'))->assertOk()->assertDontSee(route('admin.faqs.create'));
        $this->get(route('admin.faqs.create'))->assertForbidden();
        $this->post(route('admin.faqs.store'), [])->assertForbidden();
        $this->get(route('admin.faqs.edit', $faq))->assertForbidden();
        $this->delete(route('admin.faqs.destroy', $faq))->assertForbidden();
        $this->assertNotSoftDeleted($faq);
    }

    public function test_sidebar_only_lists_permitted_modules(): void
    {
        $this->actingAs(User::factory()->withPermissions(['faqs.view'])->create())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.faqs.index'))
            ->assertDontSee(route('admin.projects.index'))
            ->assertDontSee(route('admin.roles.index'));
    }

    public function test_admin_creates_role_with_selected_permissions(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post(route('admin.roles.store'), [
            'name' => ['ar' => 'مدقق', 'en' => 'Reviewer'],
            'permissions' => ['projects.view', 'stories.view', 'stories.update'],
        ])->assertRedirect(route('admin.roles.index'));

        $role = Role::query()->whereTranslation('name', 'مدقق')->sole();
        $this->assertSame(['projects.view', 'stories.view', 'stories.update'], $role->permissions());
    }

    public function test_unknown_permissions_and_duplicate_names_are_rejected(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        Role::factory()->create(['name' => ['ar' => 'محرر']]);

        $this->post(route('admin.roles.store'), ['name' => ['ar' => 'محرر'], 'permissions' => ['projects.fly']])
            ->assertSessionHasErrors(['name.ar', 'permissions.0']);
    }

    public function test_system_role_cannot_be_edited_or_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->get(route('admin.roles.edit', $admin->role))->assertForbidden();
        $this->put(route('admin.roles.update', $admin->role), ['name' => ['ar' => 'x']])->assertForbidden();
        $this->delete(route('admin.roles.destroy', $admin->role))->assertForbidden();
    }

    public function test_role_in_use_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $this->actingAs(User::factory()->admin()->create());

        $this->delete(route('admin.roles.destroy', $user->role))->assertForbidden();
        $this->assertModelExists($user->role);
    }

    public function test_user_overrides_grant_and_revoke_on_top_of_role(): void
    {
        $user = User::factory()->withPermissions(['projects.view', 'projects.update'])->create();
        $this->actingAs(User::factory()->admin()->create());

        $this->put(route('admin.users.permissions.update', $user), ['permissions' => ['projects.view', 'faqs.view']])
            ->assertRedirect(route('admin.users.permissions', $user));

        $user = $user->fresh();
        $this->assertEqualsCanonicalizing(['projects.update' => false, 'faqs.view' => true], $user->permissionOverrides());
        $this->assertTrue($user->hasPermission('faqs.view'));
        $this->assertFalse($user->hasPermission('projects.update'));
    }

    public function test_users_cannot_grant_permissions_they_do_not_have(): void
    {
        $manager = User::factory()->withPermissions(['users.view', 'users.update', 'roles.view', 'roles.create', 'faqs.view'])->create();
        $target = User::factory()->withPermissions(['faqs.view'])->create();
        $this->actingAs($manager);

        $this->put(route('admin.users.permissions.update', $target), ['permissions' => ['faqs.view', 'settings.update']])
            ->assertSessionHasErrors('permissions.1');
        $this->post(route('admin.roles.store'), ['name' => ['ar' => 'دور أقوى'], 'permissions' => ['settings.update']])
            ->assertSessionHasErrors('permissions.0');
        $this->put(route('admin.users.permissions.update', $manager), ['permissions' => []])->assertForbidden();
    }

    public function test_non_admin_cannot_manage_administrators_or_assign_their_role(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->withPermissions(['users.view', 'users.create', 'users.update'])->create();
        $this->actingAs($manager);

        $this->get(route('admin.users.edit', $admin))->assertForbidden();
        $this->patch(route('admin.users.toggle', $admin))->assertForbidden();
        $this->get(route('admin.users.permissions', $admin))->assertForbidden();
        $this->post(route('admin.users.store'), [
            'name' => 'Escalation',
            'email' => 'escalate@example.com',
            'role_id' => $admin->role_id,
            'password' => 'Another-Pass-2027!',
            'password_confirmation' => 'Another-Pass-2027!',
        ])->assertSessionHasErrors('role_id');
    }
}
