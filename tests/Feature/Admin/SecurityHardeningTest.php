<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()->withPermissions(['users.view', 'users.update', 'roles.view', 'roles.update', 'roles.delete', 'projects.view', 'projects.update'])->create();
    }

    public function test_a_manager_cannot_take_over_a_lower_role_user_holding_extra_permissions(): void
    {
        $manager = $this->manager();
        $target = User::factory()->withPermissions(['projects.view'])->create();
        $target->syncPermissions(['projects.view', 'settings.update']);

        $this->actingAs($manager)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'role_id' => $target->role_id,
                'password' => 'Taken-Over-123',
                'password_confirmation' => 'Taken-Over-123',
            ])
            ->assertForbidden();

        $this->assertFalse(Hash::check('Taken-Over-123', $target->fresh()->password));
        $this->actingAs($manager)->get(route('admin.users.edit', $target))->assertForbidden();
        $this->actingAs($manager)->patch(route('admin.users.toggle', $target))->assertForbidden();
    }

    public function test_a_manager_still_manages_genuinely_lower_users(): void
    {
        $manager = $this->manager();
        $target = User::factory()->withPermissions(['projects.view'])->create();

        $this->actingAs($manager)->get(route('admin.users.edit', $target))->assertOk();
    }

    public function test_roles_that_are_not_strictly_lower_cannot_be_edited_or_deleted(): void
    {
        $manager = $this->manager();
        $stronger = Role::factory()->withPermissions(['users.view', 'users.update', 'users.create', 'settings.update'])->create();
        $lower = Role::factory()->withPermissions(['projects.view'])->create();

        $this->actingAs($manager)->get(route('admin.roles.edit', $stronger))->assertForbidden();
        $this->actingAs($manager)
            ->put(route('admin.roles.update', $stronger), ['name' => ['ar' => 'مُفرَّغ'], 'permissions' => ['projects.view']])
            ->assertForbidden();
        $this->actingAs($manager)->delete(route('admin.roles.destroy', $stronger))->assertForbidden();

        $this->assertContains('settings.update', $stronger->fresh()->permissions());
        $this->actingAs($manager)->get(route('admin.roles.edit', $lower))->assertOk();
    }

    public function test_changing_a_users_password_signs_them_out_everywhere(): void
    {
        $owner = User::factory()->superAdmin()->create();
        $target = User::factory()->withPermissions(['projects.view'])->create(['remember_token' => 'old-token']);
        DB::table('sessions')->insert(['id' => 'stolen-session', 'user_id' => $target->id, 'payload' => '', 'last_activity' => time()]);

        $this->actingAs($owner)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'role_id' => $target->role_id,
                'password' => 'Fresh-Pass-123',
                'password_confirmation' => 'Fresh-Pass-123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(0, DB::table('sessions')->where('user_id', $target->id)->count());
        $this->assertNotSame('old-token', $target->fresh()->remember_token);
    }

    public function test_own_password_can_only_change_through_the_profile_with_the_current_password(): void
    {
        $owner = User::factory()->superAdmin()->create(['password' => 'Current-Pass-1']);

        $this->actingAs($owner)
            ->put(route('admin.users.update', $owner), [
                'name' => $owner->name,
                'email' => $owner->email,
                'role_id' => $owner->role_id,
                'password' => 'Hijacked-Pass-1',
                'password_confirmation' => 'Hijacked-Pass-1',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('Current-Pass-1', $owner->fresh()->password));
    }

    public function test_profile_password_change_rotates_the_remember_token(): void
    {
        $user = User::factory()->superAdmin()->create(['password' => 'Current-Pass-1', 'remember_token' => 'old-token']);

        $this->actingAs($user)
            ->put(route('admin.profile.password'), [
                'current_password' => 'Current-Pass-1',
                'password' => 'Brand-New-Pass-2',
                'password_confirmation' => 'Brand-New-Pass-2',
            ])
            ->assertRedirect(route('admin.profile.edit'));

        $this->assertNotSame('old-token', $user->fresh()->remember_token);
    }

    public function test_login_is_throttled_per_ip_even_when_the_email_keeps_changing(): void
    {
        for ($attempt = 1; $attempt <= 20; $attempt++) {
            $this->post(route('login'), ['email' => "guess{$attempt}@example.test", 'password' => 'wrong-pass'])
                ->assertSessionHasErrors('email');
        }

        $user = User::factory()->create(['password' => 'Right-Pass-1']);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'Right-Pass-1'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
