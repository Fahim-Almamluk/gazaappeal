<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\View\View;

final class StoryController extends Controller
{
    public function index(): View
    {
        return view('site.stories.index', [
            'stories' => Story::query()->published()->latest()->paginate(9),
        ]);
    }

    public function show(Story $story): View
    {
        abort_unless($story->is_published, 404);

        return view('site.stories.show', [
            'story' => $story->load(['project' => fn ($query) => $query->published()]),
        ]);
    }
}
