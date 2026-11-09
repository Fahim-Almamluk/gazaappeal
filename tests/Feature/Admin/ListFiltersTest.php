<?php

namespace Tests\Feature\Admin;

use App\Enums\DonationMethodType;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\GalleryItem;
use App\Models\Project;
use App\Models\Story;
use App\Models\User;
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
            ->assertDontSee('قصة المدرسة');
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
