<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryItemRequest;
use App\Models\GalleryItem;
use App\Support\ImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class GalleryItemController extends Controller
{
    private const string FOLDER = 'gallery';

    public function __construct(private readonly ImageStore $images) {}

    public function index(Request $request): View
    {
        $items = GalleryItem::query()
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(24)
            ->withQueryString();

        return view('admin.gallery.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.gallery.form', [
            'item' => new GalleryItem(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(GalleryItemRequest $request): RedirectResponse
    {
        $item = new GalleryItem($request->safe()->except('image'));
        $item->image = $this->images->store($request->file('image'), self::FOLDER);
        $item->save();

        return to_route('admin.gallery.index')->with('success', __('admin.gallery.created'));
    }

    public function edit(GalleryItem $galleryItem): View
    {
        return view('admin.gallery.form', ['item' => $galleryItem]);
    }

    public function update(GalleryItemRequest $request, GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->fill($request->safe()->except('image'));
        $galleryItem->image = $this->images->replace($galleryItem->image, $request->file('image'), self::FOLDER);
        $galleryItem->save();

        return to_route('admin.gallery.index')->with('success', __('admin.gallery.updated'));
    }

    public function destroy(GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->delete();

        return to_route('admin.gallery.index')->with('success', __('admin.gallery.deleted'));
    }
}
