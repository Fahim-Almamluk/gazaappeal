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
        $this->assertStringContainsString('<header class="relative overflow-hidden rounded-card', $html);
    }

    public function test_detail_pages_use_the_same_header_card_and_no_inline_error_alert(): void
    {
        $volunteer = Volunteer::factory()->create();

        $html = $this->get(route('admin.volunteers.show', $volunteer))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'aria-label="breadcrumb"'));
        $this->assertStringContainsString('rounded-card border border-line bg-surface shadow-card', $html);
    }

    public function test_index_pages_have_no_breadcrumb(): void
    {
        $this->get(route('admin.users.index'))->assertOk()->assertDontSee('aria-label="breadcrumb"', false);
    }

    public function test_language_switch_shows_the_target_language_code_and_name(): void
    {
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('>EN</span>', false)->assertSee('English');

        $this->withSession(['locale' => 'en'])->get(route('admin.dashboard'))->assertOk()->assertSee('>ع</span>', false)->assertSee('العربية');
    }
}
