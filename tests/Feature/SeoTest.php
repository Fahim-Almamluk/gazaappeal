<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_hides_the_admin_and_points_to_the_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee(route('sitemap'), false);
    }

    public function test_sitemap_lists_public_pages_and_only_published_projects(): void
    {
        $published = Project::factory()->create();
        $hidden = Project::factory()->hidden()->create();

        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8')
            ->assertSee(route('home', ['locale' => 'ar']), false)
            ->assertSee(route('home', ['locale' => 'en']), false)
            ->assertSee(route('projects.show', ['locale' => 'ar', 'project' => $published->id]), false)
            ->assertDontSee(route('projects.show', ['locale' => 'ar', 'project' => $hidden->id]), false)
            ->assertDontSee('/admin', false);
    }

    public function test_pages_expose_canonical_and_social_image_tags(): void
    {
        $this->get(route('home', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee('rel="canonical"', false)
            ->assertSee('property="og:image"', false);
    }
}
