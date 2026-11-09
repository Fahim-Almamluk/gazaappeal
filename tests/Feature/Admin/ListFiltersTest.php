<?php

namespace Tests\Feature\Admin;

use App\Enums\DonationMethodType;
use App\Models\ContactMessage;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\GalleryItem;
use App\Models\Project;
use App\Models\Story;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->superAdmin()->create())->withSession(['locale' => 'ar']);
    }

    public function test_projects_are_filtered_by_priority(): void
    {
        Project::factory()->urgent()->create(['title' => ['ar' => 'مشروع عاجل أول']]);
        Project::factory()->create(['title' => ['ar' => 'مشروع عادي ثانٍ']]);

        $this->get(route('admin.projects.index', ['priority' => 'urgent']))
            ->assertOk()
            ->assertSee('مشروع عاجل أول')
            ->assertDontSee('مشروع عادي ثانٍ');

        $this->get(route('admin.projects.index', ['priority' => 'regular']))
            ->assertOk()
            ->assertSee('مشروع عادي ثانٍ')
            ->assertDontSee('مشروع عاجل أول');

        $this->get(route('admin.projects.index', ['priority' => 'invalid']))
            ->assertOk()
            ->assertSee('مشروع عاجل أول')
            ->assertSee('مشروع عادي ثانٍ');
    }

    public function test_stories_are_filtered_by_project(): void
    {
        $first = Project::factory()->create(['title' => ['ar' => 'مشروع المستشفى']]);
        $second = Project::factory()->create(['title' => ['ar' => 'مشروع المدرسة']]);
        Story::factory()->for($first)->create(['title' => ['ar' => 'قصة المستشفى']]);
        Story::factory()->for($second)->create(['title' => ['ar' => 'قصة المدرسة']]);

        $this->get(route('admin.stories.index', ['project' => $first->id]))
            ->assertOk()
            ->assertSee('قصة المستشفى')
            ->assertDontSee('قصة المدرسة')
            ->assertSee('مشروع المدرسة')
            ->assertDontSee('{&quot;ar&quot;', false);

        $this->get(route('admin.stories.create'))
            ->assertOk()
            ->assertSee('مشروع المستشفى')
            ->assertDontSee('{&quot;ar&quot;', false);
    }

    public function test_long_filter_lists_get_a_searchable_dropdown_and_short_ones_stay_native(): void
    {
        $projects = Project::factory()->count(8)->create();
        Story::factory()->for($projects->first())->create();

        $this->get(route('admin.stories.index'))
            ->assertOk()
            ->assertSee('x-data="combobox"', false)
            ->assertSee('data-option', false);

        $this->get(route('admin.projects.index'))
            ->assertOk()
            ->assertDontSee('x-data="combobox"', false);
    }

    public function test_damages_are_searched_by_item(): void
    {
        Damage::factory()->create(['item' => ['ar' => 'محول رئيسي']]);
        Damage::factory()->create(['item' => ['ar' => 'كابلات أرضية']]);

        $this->get(route('admin.damages.index', ['q' => 'محول']))
            ->assertOk()
            ->assertSee('محول رئيسي')
            ->assertDontSee('كابلات أرضية');
    }

    public function test_donation_methods_are_filtered_by_type_and_name(): void
    {
        DonationMethod::factory()->create(['name' => ['ar' => 'منصة إنساني']]);
        DonationMethod::factory()->bank()->create(['name' => ['ar' => 'حساب بنك فلسطين']]);

        $this->get(route('admin.donation-methods.index', ['type' => DonationMethodType::BankTransfer->value]))
            ->assertOk()
            ->assertSee('حساب بنك فلسطين')
            ->assertDontSee('منصة إنساني');

        $this->get(route('admin.donation-methods.index', ['q' => 'إنساني']))
            ->assertOk()
            ->assertSee('منصة إنساني')
            ->assertDontSee('حساب بنك فلسطين');
    }

    public function test_volunteers_and_messages_are_filtered_by_period(): void
    {
        Volunteer::factory()->create(['name' => 'متطوع حديث', 'created_at' => now()->subDays(2)]);
        Volunteer::factory()->create(['name' => 'متطوع قديم', 'created_at' => now()->subDays(60)]);
        ContactMessage::factory()->create(['name' => 'مرسل حديث', 'created_at' => now()->subDay()]);
        ContactMessage::factory()->create(['name' => 'مرسل قديم', 'created_at' => now()->subDays(60)]);

        $this->get(route('admin.volunteers.index', ['period' => 7]))
            ->assertOk()
            ->assertSee('متطوع حديث')
            ->assertDontSee('متطوع قديم');

        $this->get(route('admin.volunteers.index', ['period' => 90]))
            ->assertSee('متطوع قديم');

        $this->get(route('admin.messages.index', ['period' => 30]))
            ->assertOk()
            ->assertSee('مرسل حديث')
            ->assertDontSee('مرسل قديم');
    }

    public function test_columns_can_be_sorted_and_invalid_sort_is_ignored(): void
    {
        Volunteer::factory()->create(['name' => 'باسل']);
        Volunteer::factory()->create(['name' => 'سمر']);

        $this->get(route('admin.volunteers.index', ['sort' => 'name', 'dir' => 'asc']))
            ->assertOk()
            ->assertSeeInOrder(['باسل', 'سمر']);

        $this->get(route('admin.volunteers.index', ['sort' => 'name', 'dir' => 'desc']))
            ->assertOk()
            ->assertSeeInOrder(['سمر', 'باسل']);

        $this->get(route('admin.volunteers.index', ['sort' => 'password; drop table']))
            ->assertOk();
    }

    public function test_gallery_is_searched_by_caption(): void
    {
        GalleryItem::factory()->create(['caption' => ['ar' => 'تركيب الألواح']]);
        GalleryItem::factory()->create(['caption' => ['ar' => 'توزيع المساعدات']]);

        $this->get(route('admin.gallery.index', ['q' => 'الألواح']))
            ->assertOk()
            ->assertSee('تركيب الألواح')
            ->assertDontSee('توزيع المساعدات');
    }
}
