<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private const string NEW_PASSWORD = 'Another-Pass-2027!';

    public function test_editor_cannot_manage_users_or_settings(): void
    {
        $editor = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($editor);

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.users.create'))->assertForbidden();
        $this->post(route('admin.users.store'), [])->assertForbidden();
        $this->put(route('admin.users.update', $other), [])->assertForbidden();
        $this->patch(route('admin.users.toggle', $other))->assertForbidden();
        $this->get(route('admin.settings.edit'))->assertForbidden();
        $this->put(route('admin.settings.update'), [])->assertForbidden();
        $this->get(route('admin.pages.edit'))->assertForbidden();
        $this->put(route('admin.pages.update'), [])->assertForbidden();
    }

    public function test_editor_does_not_see_system_links(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertDontSee(route('admin.users.index'))
            ->assertDontSee(route('admin.settings.edit'));
    }

    public function test_admin_creates_user_with_strong_password(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $role = Role::factory()->editor()->create();

        $this->post(route('admin.users.store'), [
            'name' => 'محرر جديد',
            'email' => 'editor@gedco.ps',
            'role_id' => $role->id,
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->post(route('admin.users.store'), [
            'name' => 'محرر جديد',
            'email' => 'editor@gedco.ps',
            'role_id' => $role->id,
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ])->assertRedirect(route('admin.users.index'));

        $user = User::query()->where('email', 'editor@gedco.ps')->sole();
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->password));
        $this->assertTrue($user->role->is($role));
        $this->assertTrue($user->is_active);
    }

    public function test_admin_cannot_deactivate_self_or_change_own_role(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $this->patch(route('admin.users.toggle', $admin))->assertForbidden();

        $this->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => Role::factory()->editor()->create()->id,
        ])->assertSessionHasErrors('role_id');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_deactivates_another_user(): void
    {
        $editor = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.users.toggle', $editor))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($editor->fresh()->is_active);
    }

    public function test_admin_updates_site_settings(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.settings.update'), [
                'contact_phone' => '+970 8 283 7521',
                'contact_email' => 'appeal@gedco.ps',
                'impact_people' => '2.3',
                'impact_duration' => '20',
                'social_facebook' => 'https://facebook.com/gedco',
                'social_x' => 'http://insecure.example',
            ])
            ->assertSessionHasErrors('social_x');

        $this->put(route('admin.settings.update'), [
            'hero_tagline' => 'النور يعود',
            'hero_tagline_en' => 'Light returns',
            'hero_subtitle' => 'طاقة شمسية للمياه والصحة.',
            'contact_email' => 'appeal@gedco.ps',
            'impact_people' => '2.3',
            'impact_duration' => '20',
            'social_facebook' => 'https://facebook.com/gedco',
            'social_instagram' => '',
            'social_x' => '',
            'social_tiktok' => '',
            'social_youtube' => '',
            'social_telegram' => '',
        ])->assertRedirect(route('admin.settings.edit'));

        $settings = app(SiteSettings::class);
        $this->assertSame('appeal@gedco.ps', $settings->get('contact_email'));
        $this->assertSame(['facebook' => 'https://facebook.com/gedco'], $settings->socials());
        $this->get('/ar')->assertSee('https://facebook.com/gedco');
        $this->get('/en')->assertSee('Light')->assertSee('returns');
    }

    public function test_admin_updates_page_content(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->put(route('admin.pages.update'), ['page_about_intro' => str_repeat('أ', 701)])
            ->assertSessionHasErrors('page_about_intro');

        $this->put(route('admin.pages.update'), [
            'page_about_intro' => 'نص تعريفي جديد للشركة.',
            'page_about_intro_en' => 'A new company introduction.',
            'page_losses_status_plant_state' => 'تعمل جزئيًا',
            'page_losses_cta_title' => '',
        ])->assertRedirect(route('admin.pages.edit'));

        $this->assertDatabaseMissing('settings', ['key' => 'page_losses_cta_title']);

        $this->get('/ar/about')->assertSee('نص تعريفي جديد للشركة.');
        $this->get('/en/about')->assertSee('A new company introduction.');
        $this->get('/ar/losses')->assertSee('تعمل جزئيًا')->assertSee(__('site.losses.cta.title', [], 'ar'));

        $this->put(route('admin.pages.update'), ['page_about_intro' => ''])->assertRedirect(route('admin.pages.edit'));

        $this->get('/ar/about')->assertDontSee('نص تعريفي جديد للشركة.')->assertSee(__('site.about.intro', [], 'ar'));
    }

    public function test_user_changes_own_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('admin.profile.password'), [
                'current_password' => 'wrong',
                'password' => self::NEW_PASSWORD,
                'password_confirmation' => self::NEW_PASSWORD,
            ])
            ->assertSessionHasErrorsIn('password', 'current_password');

        $this->put(route('admin.profile.password'), [
            'current_password' => 'Secret-Pass-2026',
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ])->assertRedirect(route('admin.profile.edit'));

        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
    }
}
