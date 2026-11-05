<?php

namespace Tests\Feature;

use App\Enums\DamageCategory;
use App\Enums\Governorate;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Loss;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        $paths = ['home' => '', 'about' => '/about', 'losses' => '/losses', 'projects' => '/projects', 'stories' => '/stories', 'gallery' => '/gallery', 'donate' => '/donate', 'faq' => '/faq', 'privacy' => '/privacy', 'volunteer' => '/volunteer', 'contact' => '/contact'];

        return collect(['ar', 'en'])
            ->crossJoin(array_keys($paths))
            ->mapWithKeys(fn (array $pair): array => ["{$pair[1]} ({$pair[0]})" => ["/{$pair[0]}{$paths[$pair[1]]}"]])
            ->all();
    }

    #[DataProvider('pages')]
    public function test_page_renders_when_empty(string $path): void
    {
        $this->get($path)->assertOk();
    }

    #[DataProvider('pages')]
    public function test_page_renders_with_content(string $path): void
    {
        $project = Project::factory()->urgent()->create();
        Story::factory()->for($project)->create();
        Loss::factory()->create();
        Damage::factory()->create();
        Partner::factory()->create();
        Faq::factory()->create();
        DonationMethod::factory()->create();
        DonationMethod::factory()->bank()->create();

        $this->get($path)->assertOk();
    }

    public function test_home_shows_only_published_urgent_projects(): void
    {
        $visible = Project::factory()->urgent()->create();
        $hidden = Project::factory()->urgent()->hidden()->create();

        $this->get('/ar')
            ->assertSee($visible->title)
            ->assertDontSee($hidden->title);
    }

    public function test_home_map_totals_damages_per_governorate(): void
    {
        Damage::factory()->create(['governorate' => Governorate::Rafah, 'category' => DamageCategory::Networks, 'amount' => 12_000_000]);
        Damage::factory()->create(['governorate' => Governorate::Rafah, 'category' => DamageCategory::Vehicles, 'amount' => 3_500_000]);
        Damage::factory()->create(['governorate' => Governorate::Gaza, 'category' => DamageCategory::Networks, 'amount' => 900_000]);

        $this->get('/ar')
            ->assertSee('$15.5M')
            ->assertSee('$16.4M')
            ->assertSee('data-governorate="rafah"', false)
            ->assertSee('$900K');
    }

    public function test_detail_pages_use_numeric_ids_in_links(): void
    {
        $project = Project::factory()->create(['title' => ['ar' => 'محطات الطاقة للرعاية الطبية']]);
        $story = Story::factory()->create();

        $this->assertSame(url("/ar/projects/{$project->id}"), route('projects.show', $project));
        $this->assertSame(url("/en/stories/{$story->id}"), route('stories.show', ['locale' => 'en', 'story' => $story]));
        $this->get("/ar/projects/{$project->id}")->assertOk()->assertSee('محطات الطاقة للرعاية الطبية');
        $this->get('/ar/projects/not-a-number')->assertNotFound();
    }

    public function test_listing_pages_paginate_with_numbered_links(): void
    {
        Project::factory()->count(10)->create();
        Story::factory()->count(10)->create();
        GalleryItem::factory()->count(15)->create();

        foreach (['projects.index', 'stories.index', 'gallery'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('rel="next"', false)
                ->assertSee('aria-current="page"', false);

            $this->get(route($route, ['page' => 2]))->assertOk()->assertSee('rel="prev"', false);
        }
    }

    public function test_gallery_shows_only_published_items(): void
    {
        GalleryItem::factory()->create(['caption' => 'صورة ظاهرة', 'is_published' => true]);
        GalleryItem::factory()->create(['caption' => 'صورة مخفية', 'is_published' => false]);

        $this->get(route('gallery'))->assertOk()->assertSee('صورة ظاهرة')->assertDontSee('صورة مخفية');
        $this->get(route('home'))->assertOk()->assertSee('صورة ظاهرة')->assertDontSee('صورة مخفية');
    }

    public function test_home_shows_only_published_partners(): void
    {
        Partner::factory()->create(['name' => 'شريك ظاهر', 'website' => 'https://partner.example']);
        Partner::factory()->create(['name' => 'شريك مخفي', 'is_published' => false]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('شريك ظاهر')
            ->assertSee('https://partner.example')
            ->assertDontSee('شريك مخفي');
    }

    public function test_hidden_project_and_story_are_not_found(): void
    {
        $project = Project::factory()->hidden()->create();
        $story = Story::factory()->create(['is_published' => false]);

        $this->get(route('projects.show', $project))->assertNotFound();
        $this->get(route('stories.show', $story))->assertNotFound();
    }

    public function test_donate_page_shows_bank_details(): void
    {
        $bank = DonationMethod::factory()->bank()->create();

        $this->get('/ar/donate')->assertSee($bank->bankDetail('iban'));
    }

    public function test_donate_page_shows_tabs_for_several_bank_accounts(): void
    {
        DonationMethod::factory()->bank()->create(['bank_details' => ['bank_name' => 'بنك أول', 'iban' => 'PS05PALS045400274280013000000']]);
        DonationMethod::factory()->bank()->create(['bank_details' => ['bank_name' => 'بنك ثان', 'iban' => 'PS11QUDS000000000000000000000']]);

        $this->get('/ar/donate')
            ->assertOk()
            ->assertSee('role="tablist"', false)
            ->assertSee('بنك أول')
            ->assertSee('بنك ثان')
            ->assertSee('PS05 PALS 0454 0027 4280 0130 0000 0');
    }

    public function test_donate_page_renders_qr_codes(): void
    {
        DonationMethod::factory()->create(['url' => 'https://donate.example.org/gaza']);
        DonationMethod::factory()->bank()->create();

        $html = $this->get('/ar/donate')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'viewBox="0 0 200 200"') + substr_count($html, 'viewBox="0 0 220 220"'));
    }

    public function test_responses_carry_security_headers(): void
    {
        $this->get('/ar')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_root_redirects_to_the_preferred_language(): void
    {
        $this->get('/', ['Accept-Language' => 'en-GB,en;q=0.9'])->assertRedirect('/en');
        $this->flushSession();
        $this->get('/', ['Accept-Language' => 'ar'])->assertRedirect('/ar');
    }

    public function test_chosen_language_overrides_the_browser_language(): void
    {
        $this->get('/en/about')->assertOk();

        $this->get('/', ['Accept-Language' => 'ar'])->assertRedirect('/en');
        $this->get('/login')->assertSee('<html lang="en" dir="ltr"', false);
    }

    public function test_unprefixed_links_redirect_to_the_localized_page(): void
    {
        $this->get('/stories?page=2', ['Accept-Language' => 'ar'])->assertRedirect('/ar/stories?page=2');
    }

    public function test_english_pages_render_left_to_right_with_alternates(): void
    {
        $this->get('/en/projects')
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr"', false)
            ->assertSee('hreflang="ar" href="'.url('/ar/projects').'"', false)
            ->assertSee('Projects');

        $this->get('/ar/projects')->assertSee('<html lang="ar" dir="rtl"', false);
    }

    public function test_content_is_shown_in_the_visitor_language_with_arabic_fallback(): void
    {
        $translated = Project::factory()->urgent()->create(['title' => ['ar' => 'مياه الشرب', 'en' => 'Drinking water']]);
        $arabicOnly = Project::factory()->urgent()->create(['title' => ['ar' => 'آبار المياه']]);

        $this->get('/en')->assertSee('Drinking water')->assertSee('آبار المياه')->assertDontSee('مياه الشرب');
        $this->get('/ar')->assertSee('مياه الشرب')->assertDontSee('Drinking water');
        $this->get(route('projects.show', ['locale' => 'en', 'project' => $translated]))->assertOk()->assertSee('Drinking water');
        $this->assertSame('آبار المياه', $arabicOnly->translation('title', 'en'));
    }

    public function test_language_switch_keeps_the_current_page(): void
    {
        $story = Story::factory()->create(['title' => ['ar' => 'قصة نجاح', 'en' => 'A success story']]);

        $this->get(route('stories.show', ['locale' => 'ar', 'story' => $story]))
            ->assertSee('href="'.route('stories.show', ['locale' => 'en', 'story' => $story]).'"', false);
    }
}
