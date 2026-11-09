<?php

namespace Tests\Feature\Admin;

use App\Models\MenuItem;
use App\Models\Project;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Story;
use App\Models\User;
use App\Support\HomeSections;
use App\Support\Permissions;
use App\Support\SiteSettings;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class SiteLayoutTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        return User::factory()->superAdmin()->create();
    }

    private function menuPayload(array $overrides = []): array
    {
        return [
            'type' => 'page',
            'page' => 'faq',
            'parent_id' => '',
            'is_visible' => '1',
            ...$overrides,
        ];
    }

    public function test_layout_screens_need_their_own_permissions(): void
    {
        $item = MenuItem::query()->firstOrFail();

        foreach ([User::factory()->administrator()->create(), User::factory()->create()] as $user) {
            $this->actingAs($user);

            $this->get(route('admin.menu.index'))->assertForbidden();
            $this->get(route('admin.menu.create'))->assertForbidden();
            $this->post(route('admin.menu.store'), $this->menuPayload())->assertForbidden();
            $this->delete(route('admin.menu.destroy', $item))->assertForbidden();
            $this->putJson(route('admin.menu.reorder'), ['items' => [['id' => $item->id, 'visible' => true]]])->assertForbidden();
            $this->post(route('admin.menu.reset'))->assertForbidden();
            $this->get(route('admin.home-sections.edit'))->assertForbidden();
            $this->post(route('admin.home-sections.reset'))->assertForbidden();
        }

        $this->actingAs($this->superAdmin());

        $this->get(route('admin.menu.index'))->assertOk()->assertSee(route('admin.menu.create'), false);
        $this->get(route('admin.menu.create'))->assertOk();
        $this->get(route('admin.menu.edit', $item))->assertOk();
        $this->get(route('admin.home-sections.edit'))->assertOk();
    }

    public function test_layout_tabs_follow_the_granted_permissions(): void
    {
        $this->actingAs(User::factory()->administrator()->create())
            ->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertDontSee(route('admin.menu.index'))
            ->assertDontSee(route('admin.home-sections.edit'));

        $role = Role::factory()->administrator()->create();
        $role->syncPermissions([...$role->permissions(), 'site_menu.view', 'site_menu.update']);

        $this->actingAs(User::factory()->create(['role_id' => $role]))
            ->get(route('admin.menu.index'))
            ->assertOk()
            ->assertSee(route('admin.settings.edit'))
            ->assertDontSee(route('admin.home-sections.edit'));

        $this->actingAs($this->superAdmin());

        foreach ([route('admin.settings.edit'), route('admin.menu.index'), route('admin.home-sections.edit')] as $url) {
            $this->get($url)->assertOk()->assertSee(route('admin.menu.index'))->assertSee(route('admin.home-sections.edit'));
        }

        $this->get(route('admin.dashboard'))->assertDontSee(route('admin.menu.index'));
    }

    public function test_site_settings_item_reaches_the_first_permitted_tab(): void
    {
        $user = User::factory()->create(['role_id' => Role::factory()->withPermissions(['home_sections.update'])]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.home-sections.edit'))
            ->assertDontSee(route('admin.settings.edit'));

        $this->get(route('admin.home-sections.edit'))->assertOk();
    }

    public function test_only_the_super_admin_sees_and_grants_the_layout_permissions(): void
    {
        $admin = User::factory()->administrator()->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.roles.create'))
            ->assertOk()
            ->assertDontSee('site_menu.view')
            ->assertDontSee('home_sections.update');

        $this->post(route('admin.roles.store'), ['name' => ['ar' => 'دور جديد'], 'permissions' => ['projects.view', 'site_menu.view']])
            ->assertSessionHasErrors('permissions.1');
        $this->put(route('admin.users.permissions.update', $target), ['permissions' => ['site_menu.update']])
            ->assertSessionHasErrors('permissions.0');

        $this->actingAs($this->superAdmin())
            ->get(route('admin.roles.create'))
            ->assertSee('site_menu.view')
            ->assertSee('home_sections.update');

        $this->put(route('admin.users.permissions.update', $target), ['permissions' => ['home_sections.update']])->assertRedirect();
        $this->assertTrue($target->fresh()->hasPermission('home_sections.update'));
    }

    public function test_admin_holding_layout_permissions_cannot_grant_or_strip_them(): void
    {
        $role = Role::factory()->administrator()->create();
        $role->syncPermissions([...$role->permissions(), 'site_menu.view', 'site_menu.update']);
        $admin = User::factory()->create(['role_id' => $role]);
        $editor = Role::factory()->withPermissions(['projects.view', 'site_menu.view'])->create();
        $target = User::factory()->create(['role_id' => $editor]);

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), ['name' => ['ar' => 'دور جديد'], 'permissions' => ['site_menu.view']])
            ->assertSessionHasErrors('permissions.0');

        $this->put(route('admin.users.permissions.update', $target), ['permissions' => ['projects.view']])->assertRedirect();
        $this->assertTrue($target->fresh()->hasPermission('site_menu.view'));

        $this->put(route('admin.roles.update', $editor), ['name' => $editor->translations('name'), 'permissions' => ['projects.view', 'faqs.view']])->assertRedirect();
        $this->assertContains('site_menu.view', $editor->fresh()->permissions());
        $this->assertContains('faqs.view', $editor->fresh()->permissions());
    }

    public function test_default_admin_role_does_not_include_the_layout_permissions(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = Role::query()->whereTranslation('name', 'أدمن')->sole();

        $this->assertSame([], array_values(array_intersect($admin->permissions(), app(Permissions::class)->restricted())));
        $this->assertContains('settings.update', $admin->permissions());
    }

    public function test_default_menu_is_seeded_in_both_languages(): void
    {
        $this->get('/ar')->assertOk()->assertSeeInOrder(['الرئيسية', 'من نحن', 'الخسائر', 'المشاريع']);
        $this->get('/en')->assertOk()->assertSeeInOrder(['Home', 'About', 'Losses', 'Projects']);
    }

    public function test_dropdown_items_render_for_both_languages(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('admin.menu.store'), $this->menuPayload([
            'type' => 'group',
            'page' => '',
            'label' => ['ar' => 'عن الحملة', 'en' => 'About us'],
        ]))->assertRedirect(route('admin.menu.index'));

        $group = MenuItem::query()->where('page', null)->sole();

        $this->post(route('admin.menu.store'), $this->menuPayload(['parent_id' => (string) $group->id, 'page' => 'faq']))
            ->assertRedirect(route('admin.menu.index'));
        $this->post(route('admin.menu.store'), $this->menuPayload([
            'type' => 'url',
            'page' => '',
            'url' => 'https://example.com/partners',
            'parent_id' => (string) $group->id,
            'label' => ['ar' => 'شركاؤنا', 'en' => 'Our partners'],
        ]))->assertRedirect(route('admin.menu.index'));

        $this->get('/ar')->assertOk()->assertSeeInOrder(['عن الحملة', 'الأسئلة الشائعة', 'شركاؤنا'])->assertSee('https://example.com/partners');
        $this->get('/en')->assertOk()->assertSeeInOrder(['About us', 'FAQ', 'Our partners']);
    }

    public function test_reorder_and_visibility_are_applied_to_the_site(): void
    {
        $this->actingAs($this->superAdmin());
        $items = MenuItem::query()->roots()->ordered()->get();

        $payload = $items->reverse()->values()->map(fn (MenuItem $item): array => ['id' => $item->id, 'visible' => $item->page !== 'about'])->all();

        $this->putJson(route('admin.menu.reorder'), ['items' => $payload])->assertNoContent();

        $this->get('/en')->assertOk()->assertSeeInOrder(['Contact', 'Success Stories', 'Volunteers', 'Projects', 'Losses', 'Home'])->assertDontSee('>About<', false);
        $this->assertSame($items->last()->id, MenuItem::query()->roots()->ordered()->firstOrFail()->id);
    }

    public function test_reorder_rejects_items_from_different_levels(): void
    {
        $this->actingAs($this->superAdmin());
        $root = MenuItem::query()->roots()->ordered()->firstOrFail();
        $child = MenuItem::factory()->create(['parent_id' => $root->id, 'page' => 'faq']);

        $this->putJson(route('admin.menu.reorder'), ['items' => [
            ['id' => $root->id, 'visible' => true],
            ['id' => $child->id, 'visible' => true],
        ]])->assertUnprocessable();

        $this->putJson(route('admin.menu.reorder'), ['items' => [['id' => 999999, 'visible' => true]]])->assertUnprocessable();
    }

    public function test_hidden_parent_hides_its_children(): void
    {
        $group = MenuItem::factory()->group('قائمة سرية', 'Secret list')->hidden()->create(['sort_order' => 20]);
        MenuItem::factory()->create(['parent_id' => $group->id, 'page' => 'faq', 'label' => ['ar' => 'بند مخفي', 'en' => 'Hidden entry']]);

        $this->get('/ar')->assertOk()->assertDontSee('قائمة سرية')->assertDontSee('بند مخفي');
    }

    public function test_group_without_children_is_not_rendered(): void
    {
        MenuItem::factory()->group('قائمة فارغة', 'Empty list')->create(['sort_order' => 20]);

        $this->get('/ar')->assertOk()->assertDontSee('قائمة فارغة');
    }

    public function test_menu_validation_rules(): void
    {
        $this->actingAs($this->superAdmin());
        $root = MenuItem::query()->roots()->ordered()->firstOrFail();
        $parent = MenuItem::factory()->group()->create(['sort_order' => 30]);
        MenuItem::factory()->create(['parent_id' => $parent->id]);

        $this->post(route('admin.menu.store'), $this->menuPayload(['type' => 'url', 'page' => '', 'url' => 'http://insecure.test', 'label' => ['ar' => 'رابط']]))
            ->assertSessionHasErrors('url');
        $this->post(route('admin.menu.store'), $this->menuPayload(['type' => 'page', 'page' => 'not-a-page']))
            ->assertSessionHasErrors('page');
        $this->post(route('admin.menu.store'), $this->menuPayload(['type' => 'group', 'page' => '']))
            ->assertSessionHasErrors('label.ar');
        $this->post(route('admin.menu.store'), $this->menuPayload(['type' => 'group', 'page' => '', 'parent_id' => (string) $root->id, 'label' => ['ar' => 'تداخل']]))
            ->assertSessionHasErrors('type');
        $this->put(route('admin.menu.update', $parent), $this->menuPayload(['type' => 'group', 'page' => '', 'parent_id' => (string) $root->id, 'label' => ['ar' => 'عن الحملة']]))
            ->assertSessionHasErrors('parent_id');
        $this->put(route('admin.menu.update', $root), $this->menuPayload(['parent_id' => (string) $root->id]))
            ->assertSessionHasErrors('parent_id');
    }

    public function test_menu_size_limits_are_enforced(): void
    {
        $this->actingAs($this->superAdmin());
        config(['navigation.limits.roots' => MenuItem::query()->roots()->count()]);

        $this->post(route('admin.menu.store'), $this->menuPayload())->assertSessionHasErrors('parent_id');

        config(['navigation.limits.roots' => 20, 'navigation.limits.children' => 1]);
        $root = MenuItem::query()->roots()->ordered()->firstOrFail();

        $this->post(route('admin.menu.store'), $this->menuPayload(['parent_id' => (string) $root->id]))->assertSessionDoesntHaveErrors();
        $this->post(route('admin.menu.store'), $this->menuPayload(['parent_id' => (string) $root->id, 'page' => 'privacy']))->assertSessionHasErrors('parent_id');
    }

    public function test_editing_moves_an_item_to_the_end_of_its_new_group(): void
    {
        $this->actingAs($this->superAdmin());
        $group = MenuItem::factory()->group()->create(['sort_order' => 30]);
        $existing = MenuItem::factory()->create(['parent_id' => $group->id, 'sort_order' => 4]);
        $moving = MenuItem::query()->roots()->where('page', 'faq')->first() ?? MenuItem::factory()->create(['page' => 'faq', 'sort_order' => 40]);

        $this->put(route('admin.menu.update', $moving), $this->menuPayload(['parent_id' => (string) $group->id]))->assertRedirect(route('admin.menu.index'));

        $this->assertSame($existing->sort_order + 1, $moving->fresh()->sort_order);
        $this->assertSame($group->id, $moving->fresh()->parent_id);
    }

    public function test_deleting_a_parent_deletes_its_children_and_reset_restores_defaults(): void
    {
        $this->actingAs($this->superAdmin());
        $group = MenuItem::factory()->group()->create(['sort_order' => 30]);
        MenuItem::factory()->create(['parent_id' => $group->id]);

        $this->delete(route('admin.menu.destroy', $group))->assertRedirect(route('admin.menu.index'));
        $this->assertSame(0, MenuItem::query()->whereNotNull('parent_id')->count());

        MenuItem::query()->delete();
        $this->get('/ar')->assertOk()->assertSee('من نحن');

        MenuItem::factory()->create(['page' => 'faq']);
        $this->post(route('admin.menu.reset'))->assertRedirect(route('admin.menu.index'));

        $this->assertSame(count(config('navigation.defaults')), MenuItem::query()->count());
        $this->assertSame(config('navigation.defaults'), MenuItem::query()->ordered()->pluck('page')->all());
    }

    public function test_empty_menu_table_falls_back_to_the_default_menu(): void
    {
        MenuItem::query()->delete();

        $this->get('/en')->assertOk()->assertSeeInOrder(['Home', 'About', 'Contact']);
    }

    public function test_menu_is_cached_and_flushed_on_change(): void
    {
        $this->actingAs($this->superAdmin());

        $this->get('/ar')->assertDontSee('/ar/gallery');

        $this->post(route('admin.menu.store'), $this->menuPayload(['page' => 'gallery']))->assertRedirect();

        $this->get('/ar')->assertSee('/ar/gallery');
    }

    public function test_home_sections_follow_the_saved_order(): void
    {
        Story::factory()->create(['title' => ['ar' => 'قصة اختبار', 'en' => 'Test story']]);
        Project::factory()->create(['is_urgent' => true]);
        $this->actingAs($this->superAdmin());

        $this->get('/en')->assertOk()->assertSeeInOrder([__('site.projects_section.subtitle', [], 'en'), __('site.stories_section.subtitle', [], 'en')]);

        $order = collect(app(HomeSections::class)->keys())->reverse()->values();
        $items = $order->map(fn (string $key): array => ['id' => $key, 'visible' => true])->all();

        $this->putJson(route('admin.home-sections.update'), ['items' => $items])->assertNoContent();

        $this->get('/en')->assertOk()->assertSeeInOrder([__('site.stories_section.subtitle', [], 'en'), __('site.projects_section.subtitle', [], 'en')]);
    }

    public function test_hidden_home_section_is_not_rendered(): void
    {
        $this->actingAs($this->superAdmin());
        $items = collect(app(HomeSections::class)->keys())->map(fn (string $key): array => ['id' => $key, 'visible' => $key !== 'stories'])->all();

        $this->putJson(route('admin.home-sections.update'), ['items' => $items])->assertNoContent();

        $this->get('/en')->assertOk()->assertDontSee(__('site.stories_section.subtitle', [], 'en'))->assertSee(__('site.volunteer_cta.title', [], 'en'));
    }

    public function test_home_sections_validation_and_reset(): void
    {
        $this->actingAs($this->superAdmin());
        $keys = app(HomeSections::class)->keys();

        $this->putJson(route('admin.home-sections.update'), ['items' => [['id' => $keys[0], 'visible' => true]]])->assertUnprocessable();
        $this->putJson(route('admin.home-sections.update'), ['items' => collect($keys)->map(fn (string $key): array => ['id' => 'unknown', 'visible' => true])->all()])->assertUnprocessable();
        $this->putJson(route('admin.home-sections.update'), ['items' => collect($keys)->map(fn (string $key): array => ['id' => $keys[0], 'visible' => true])->all()])->assertUnprocessable();

        $this->putJson(route('admin.home-sections.update'), ['items' => collect($keys)->reverse()->values()->map(fn (string $key): array => ['id' => $key, 'visible' => false])->all()])->assertNoContent();
        $this->assertSame([], app(HomeSections::class)->visible());

        $this->post(route('admin.home-sections.reset'))->assertRedirect(route('admin.home-sections.edit'));
        $this->assertSame($keys, app(HomeSections::class)->visible());
    }

    public function test_new_code_sections_and_stale_keys_are_handled(): void
    {
        $this->actingAs($this->superAdmin());
        $keys = app(HomeSections::class)->keys();

        Setting::query()->create(['key' => 'home_sections', 'value' => json_encode(['order' => ['gallery', 'removed-section'], 'hidden' => ['removed-section']])]);
        app('cache')->forget('home-sections');

        $all = collect(app(HomeSections::class)->all());

        $this->assertSame('gallery', $all->first()['key']);
        $this->assertSame(count($keys), $all->count());
        $this->assertTrue($all->every(fn (array $section): bool => $section['visible']));
    }

    public function test_logo_can_be_uploaded_and_restored_from_settings(): void
    {
        Storage::fake('public');
        $this->actingAs($this->superAdmin());

        $base = [
            'hero_tagline' => 'بعطائك يعود النور',
            'hero_subtitle' => 'مساهمتك اليوم تعيد الماء.',
            'impact_people' => '2.5',
            'impact_duration' => '12',
        ];

        $this->put(route('admin.settings.update'), [
            ...$base,
            'logo_ar' => UploadedFile::fake()->image('logo-ar.png', 300, 100),
            'logo_en' => UploadedFile::fake()->image('logo-en.png', 300, 100),
            'logo_mark' => UploadedFile::fake()->image('mark.png', 64, 64),
        ])->assertRedirect(route('admin.settings.edit'));

        $settings = app(SiteSettings::class);
        $this->assertNotNull($settings->get('logo_ar'));
        Storage::disk('public')->assertExists([$settings->get('logo_ar'), $settings->get('logo_en'), $settings->get('logo_mark')]);

        $this->get('/ar')->assertSee('storage/'.$settings->get('logo_ar'), false)->assertSee('storage/'.$settings->get('logo_mark'), false);
        $this->get('/en')->assertSee('storage/'.$settings->get('logo_en'), false)->assertDontSee('images/brand/logo-en.webp', false);

        $replaced = $settings->get('logo_ar');
        $this->put(route('admin.settings.update'), [...$base, 'logo_ar' => UploadedFile::fake()->image('new.png', 300, 100)])->assertRedirect();
        Storage::disk('public')->assertMissing($replaced);

        $removed = app(SiteSettings::class)->get('logo_en');
        $this->put(route('admin.settings.update'), [...$base, 'remove_logo_en' => '1'])->assertRedirect();
        Storage::disk('public')->assertMissing($removed);
        $this->get('/en')->assertSee('images/brand/logo-en.webp', false);
        $this->assertNotNull(app(SiteSettings::class)->get('logo_ar'));
    }

    public function test_logo_rejects_non_images_and_svg(): void
    {
        Storage::fake('public');
        $this->actingAs($this->superAdmin());

        $base = ['hero_tagline' => 'بعطائك', 'hero_subtitle' => 'مساهمتك.', 'impact_people' => '2.5', 'impact_duration' => '12'];

        $this->put(route('admin.settings.update'), [...$base, 'logo_ar' => UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml')])->assertSessionHasErrors('logo_ar');
        $this->put(route('admin.settings.update'), [...$base, 'logo_en' => UploadedFile::fake()->create('logo.pdf', 10, 'application/pdf')])->assertSessionHasErrors('logo_en');
    }
}
