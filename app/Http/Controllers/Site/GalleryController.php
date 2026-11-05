<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\View\View;

final class GalleryController extends Controller
{
    public function __invoke(): View
    {
        return view('site.gallery', [
            'items' => GalleryItem::query()->published()->ordered()->paginate(14),
        ]);
    }
}
