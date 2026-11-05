<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Enums\VolunteerStatus;
use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;
use App\Models\Volunteer;
use Illuminate\View\View;

final class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('site.about', [
            'gallery' => GalleryItem::query()->published()->ordered()->take(7)->get(),
            'figures' => [
                'projects' => Project::query()->published()->count(),
                'volunteers' => Volunteer::query()->where('status', VolunteerStatus::Accepted)->count(),
                'partners' => Partner::query()->published()->count(),
                'stories' => Story::query()->published()->count(),
            ],
        ]);
    }
}
