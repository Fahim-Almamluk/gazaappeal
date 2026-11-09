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
use App\Support\HomeSections;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __construct(private readonly HomeSections $sections) {}

    public function __invoke(): View
    {
        return view('site.home', [
            'projectsCount' => Project::query()->published()->count(),
            'sections' => collect($this->sections->visible())
                ->mapWithKeys(fn (string $section): array => [$section => $this->dataFor($section)])
                ->all(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataFor(string $section): array
    {
        return match ($section) {
            'projects' => [
                'projects' => Project::query()->with('status')->published()->urgent()->ordered()->take(6)->get(),
            ],
            'damage_map' => $this->damageMap(),
            'giving' => [
                'methods' => DonationMethod::query()->published()->ordered()->get(),
            ],
            'stories' => [
                'stories' => Story::query()->published()->latest()->take(3)->get(),
            ],
            'gallery' => [
                'gallery' => GalleryItem::query()->published()->ordered()->take(7)->get(),
            ],
            'partners' => [
                'partners' => Partner::query()->published()->ordered()->take(12)->get(['id', 'name', 'logo', 'website']),
            ],
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function damageMap(): array
    {
        $damages = Damage::query()->ordered()->get(['governorate', 'category_id', 'item', 'amount']);

        return [
            'damages' => $damages,
            'damageCategories' => Lookup::query()
                ->ofType(LookupType::DamageCategory)
                ->where(fn ($query) => $query->where('is_active', true)->orWhereIn('id', $damages->pluck('category_id')->unique()))
                ->ordered()
                ->get(),
        ];
    }
}
