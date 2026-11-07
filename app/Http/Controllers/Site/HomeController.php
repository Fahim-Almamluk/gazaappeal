<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Enums\LookupType;
use App\Http\Controllers\Controller;
use App\Models\Damage;
use App\Models\DonationMethod;
use App\Models\GalleryItem;
use App\Models\Lookup;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Story;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $damages = Damage::query()->ordered()->get(['governorate', 'category_id', 'item', 'amount']);

        return view('site.home', [
            'projects' => Project::query()->with('status')->published()->urgent()->ordered()->take(6)->get(),
            'projectsCount' => Project::query()->published()->count(),
            'damages' => $damages,
            'damageCategories' => Lookup::query()
                ->ofType(LookupType::DamageCategory)
                ->where(fn ($query) => $query->where('is_active', true)->orWhereIn('id', $damages->pluck('category_id')->unique()))
                ->ordered()
                ->get(),
            'methods' => DonationMethod::query()->published()->ordered()->get(),
            'gallery' => GalleryItem::query()->published()->ordered()->take(7)->get(),
            'stories' => Story::query()->published()->latest()->take(3)->get(),
            'partners' => Partner::query()->published()->ordered()->take(12)->get(['id', 'name', 'logo', 'website']),
        ]);
    }
}
