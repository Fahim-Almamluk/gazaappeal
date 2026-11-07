<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PageChromeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create())->withSession(['locale' => 'ar']);
    }

    public function test_form_pages_render_the_header_card_with_one_breadcrumb(): void
    {
        $html = $this->get(route('admin.users.create'))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'aria-label="breadcrumb"'));
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('<header class="rounded-panel bg-linear-to-l from-brand-700 via-brand-600 to-brand-500', $html);
    }

    public function test_detail_pages_use_the_same_header_card_and_no_inline_error_alert(): void
    {
        $volunteer = Volunteer::factory()->create();

        $html = $this->get(route('admin.volunteers.show', $volunteer))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'aria-label="breadcrumb"'));
        $this->assertStringContainsString('from-brand-700 via-brand-600 to-brand-500', $html);
    }

    public function test_index_pages_have_no_breadcrumb(): void
    {
        $this->get(route('admin.users.index'))->assertOk()->assertDontSee('aria-label="breadcrumb"', false);
    }

    public function test_language_switch_shows_the_target_language_code_and_name(): void
    {
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('hreflang="en"', false)->assertSee('>EN</span>', false)->assertSee('English');

        $this->withSession(['locale' => 'en'])->get(route('admin.dashboard'))->assertOk()->assertSee('hreflang="ar"', false)->assertSee('>ع</span>', false)->assertSee('العربية');
    }

    public function test_theme_control_is_a_single_toggle_button_without_a_menu(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-to-dark="التبديل إلى الوضع الداكن"', false)
            ->assertSee('data-to-light="التبديل إلى الوضع الفاتح"', false)
            ->assertDontSee('menuitemradio', false);

        $this->get('/ar')->assertOk()->assertSee('data-to-dark', false)->assertDontSee('menuitemradio', false);
    }

    public function test_header_actions_get_brand_friendly_button_styles(): void
    {
        $this->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('data-variant="primary"', false)
            ->assertSee('[&_[data-variant=primary]]:bg-white', false);
    }

    public function test_back_button_is_solid_red_in_every_state(): void
    {
        $html = $this->get(route('admin.users.create'))->assertOk()->getContent();

        $this->assertStringContainsString('data-variant="back" class="inline-flex', $html);
        $this->assertStringContainsString('bg-danger-solid text-white', $html);
        $this->assertStringNotContainsString('hover:bg-danger-solid', $html);
    }
}
