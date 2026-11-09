<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Story;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    private const array PAGES = ['home', 'about', 'losses', 'projects.index', 'stories.index', 'gallery', 'donate', 'faq', 'privacy', 'volunteer.create', 'contact.create'];

    public function __invoke(): Response
    {
        $entries = [];

        foreach (array_keys(config('appeal.locales')) as $locale) {
            foreach (self::PAGES as $page) {
                $entries[] = ['url' => route($page, ['locale' => $locale]), 'lastmod' => null];
            }

            foreach (Project::query()->published()->get(['id', 'updated_at']) as $project) {
                $entries[] = ['url' => route('projects.show', ['locale' => $locale, 'project' => $project->id]), 'lastmod' => $project->updated_at];
            }

            foreach (Story::query()->published()->get(['id', 'updated_at']) as $story) {
                $entries[] = ['url' => route('stories.show', ['locale' => $locale, 'story' => $story->id]), 'lastmod' => $story->updated_at];
            }
        }

        return response()
            ->view('site.sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
