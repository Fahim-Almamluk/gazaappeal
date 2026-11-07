<?php

namespace Tests\Feature\Admin;

use App\Enums\LookupType;
use App\Models\Damage;
use App\Models\Lookup;
use App\Models\Project;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LookupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create())->withSession(['locale' => 'ar']);
    }

    public function test_index_shows_a_tab_per_type_with_its_items(): void
    {
        $this->get(route('admin.lookups.index', ['type' => LookupType::DamageCategory->value]))
            ->assertOk()
            ->assertSee('الشبكات الكهربائية')
            ->assertDontSee('قيد التخطيط')
            ->assertSee(route('admin.lookups.index', ['type' => LookupType::VolunteerExpertise->value]), false);

        $this->get(route('admin.lookups.index', ['type' => 'unknown']))->assertOk()->assertSee('قيد التخطيط');
    }

    public function test_item_is_created_with_both_languages(): void
    {
        $this->post(route('admin.lookups.store'), [
            'type' => LookupType::DamageCategory->value,
            'name' => ['ar' => 'محطات التوليد', 'en' => 'Power plants'],
            'icon' => 'factory',
            'tone' => 'warning',
            'is_active' => '1',
            'sort_order' => '9',
        ])->assertRedirect(route('admin.lookups.index', ['type' => LookupType::DamageCategory->value]));

        $lookup = Lookup::query()->whereTranslation('name', 'محطات التوليد')->sole();
        $this->assertSame(LookupType::DamageCategory, $lookup->type);
        $this->assertSame(['ar' => 'محطات التوليد', 'en' => 'Power plants'], $lookup->translations('name'));
        $this->assertSame('factory', $lookup->icon);
        $this->assertSame('warning', $lookup->badgeTone());
        $this->assertTrue($lookup->is_active);
    }

    public function test_item_is_validated(): void
    {
        $this->post(route('admin.lookups.store'), [
            'type' => 'colours',
            'name' => ['en' => 'Only English'],
            'icon' => 'not-an-icon',
            'tone' => 'purple',
            'sort_order' => '0',
        ])->assertSessionHasErrors(['type', 'name.ar', 'icon', 'tone']);

        $this->assertSame(12, Lookup::query()->count());
    }

    public function test_update_keeps_the_type(): void
    {
        $lookup = Lookup::factory()->type(LookupType::ProjectStatus)->create();

        $this->put(route('admin.lookups.update', $lookup), [
            'type' => LookupType::DamageCategory->value,
            'name' => ['ar' => 'معلّق'],
            'sort_order' => '0',
        ])->assertSessionHasErrors('type');

        $this->put(route('admin.lookups.update', $lookup), [
            'name' => ['ar' => 'معلّق', 'en' => 'On hold'],
            'icon' => '',
            'sort_order' => '4',
        ])->assertRedirect();

        $lookup->refresh();
        $this->assertSame(LookupType::ProjectStatus, $lookup->type);
        $this->assertSame('معلّق', $lookup->name);
        $this->assertNull($lookup->icon);
        $this->assertFalse($lookup->is_active);
        $this->assertSame(4, $lookup->sort_order);
    }

    public function test_used_item_cannot_be_deleted(): void
    {
        $category = Lookup::factory()->type(LookupType::DamageCategory)->create();
        Damage::factory()->count(3)->create(['category_id' => $category]);

        $this->get(route('admin.lookups.index', ['type' => LookupType::DamageCategory->value]))
            ->assertSee('3 سجلات')
            ->assertDontSee('action="'.route('admin.lookups.destroy', $category).'"', false);

        $this->delete(route('admin.lookups.destroy', $category))
            ->assertRedirect(route('admin.lookups.index', ['type' => LookupType::DamageCategory->value]))
            ->assertSessionHas('error', 'لا يمكن الحذف: العنصر مستخدم في 3 سجلات، عطّله بدل حذفه.');

        $this->assertModelExists($category);
    }

    public function test_trashed_projects_still_block_deleting_their_status(): void
    {
        $status = Lookup::factory()->type(LookupType::ProjectStatus)->create();
        Project::factory()->create(['status_id' => $status])->delete();

        $this->delete(route('admin.lookups.destroy', $status))->assertSessionHas('error');

        $this->assertModelExists($status);
    }

    public function test_unused_item_is_deleted(): void
    {
        $lookup = Lookup::factory()->type(LookupType::VolunteerExpertise)->create();

        $this->delete(route('admin.lookups.destroy', $lookup))->assertSessionHas('success');

        $this->assertModelMissing($lookup);
    }

    public function test_inactive_item_is_hidden_from_new_entries_but_kept_on_old_records(): void
    {
        $retired = Lookup::factory()->type(LookupType::ProjectStatus)->inactive()->create(['name' => ['ar' => 'حالة قديمة']]);
        $project = Project::factory()->create(['status_id' => $retired]);

        $this->get(route('admin.projects.create'))->assertOk()->assertDontSee('حالة قديمة');
        $this->get(route('admin.projects.edit', $project))->assertOk()->assertSee('حالة قديمة');
        $this->get(route('admin.projects.index'))->assertOk()->assertSee('حالة قديمة');

        $data = [
            'title' => ['ar' => 'مشروع'],
            'summary' => ['ar' => 'ملخص'],
            'body' => ['ar' => 'نص'],
            'raised_amount' => '0',
            'status_id' => $retired->id,
            'sort_order' => '0',
        ];

        $this->post(route('admin.projects.store'), $data)->assertSessionHasErrors('status_id');
        $this->put(route('admin.projects.update', $project), $data)->assertSessionDoesntHaveErrors();
    }

    public function test_inactive_expertise_is_not_offered_to_volunteers(): void
    {
        $retired = Lookup::factory()->type(LookupType::VolunteerExpertise)->inactive()->create(['name' => ['ar' => 'خبرة متوقفة']]);
        Volunteer::factory()->create(['expertise_id' => $retired]);

        $this->get(route('volunteer.create'))->assertOk()->assertDontSee('خبرة متوقفة');
        $this->get(route('admin.volunteers.index'))->assertOk()->assertSee('خبرة متوقفة');
        $this->post(route('volunteer.store'), [
            'name' => 'سارة أحمد',
            'email' => 'sara@example.com',
            'phone' => '+970 59 123 4567',
            'country' => 'فلسطين',
            'expertise_id' => $retired->id,
            'experience_years' => 8,
            'message' => 'مهندسة طاقة شمسية بخبرة في تصميم الأنظمة الهجينة للمرافق الصحية.',
        ])->assertSessionHasErrors('expertise_id');
    }

    public function test_damage_map_uses_dynamic_categories(): void
    {
        $plants = Lookup::factory()->type(LookupType::DamageCategory)->create(['name' => ['ar' => 'محطات التوليد'], 'icon' => 'factory']);
        $retired = Lookup::factory()->type(LookupType::DamageCategory)->inactive()->create(['name' => ['ar' => 'تصنيف مؤرشف']]);
        Lookup::factory()->type(LookupType::DamageCategory)->inactive()->create(['name' => ['ar' => 'تصنيف فارغ']]);
        Damage::factory()->create(['category_id' => $plants, 'item' => ['ar' => 'محطة غزة']]);
        Damage::factory()->create(['category_id' => $retired, 'item' => ['ar' => 'بند قديم']]);

        $this->get('/ar')
            ->assertOk()
            ->assertSee('محطات التوليد')
            ->assertSee('data-category="'.$plants->id.'"', false)
            ->assertSee('محطة غزة')
            ->assertSee('تصنيف مؤرشف')
            ->assertDontSee('تصنيف فارغ');
    }

    public function test_users_without_permission_cannot_manage_items(): void
    {
        $lookup = Lookup::query()->firstOrFail();
        $this->actingAs(User::factory()->withPermissions(['lookups.view'])->create());

        $this->get(route('admin.lookups.index'))->assertOk()->assertDontSee(route('admin.lookups.create', ['type' => LookupType::ProjectStatus->value]), false);
        $this->get(route('admin.lookups.create'))->assertForbidden();
        $this->delete(route('admin.lookups.destroy', $lookup))->assertForbidden();
    }
}
