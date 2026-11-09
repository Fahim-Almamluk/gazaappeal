<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Http\Requests\Admin\MenuOrderRequest;
use App\Models\MenuItem;
use App\Support\SiteMenu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class MenuController extends Controller
{
    public function __construct(private readonly SiteMenu $menu) {}

    public function index(): View
    {
        return view('admin.menu.index', [
            'items' => MenuItem::query()->roots()->with('children')->ordered()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $parent = MenuItem::query()->roots()->whereKey($request->integer('parent'))->first();

        return view('admin.menu.form', [
            'item' => new MenuItem(['parent_id' => $parent?->id, 'is_visible' => true]),
            'parents' => $this->parents(),
        ]);
    }

    public function store(MenuItemRequest $request): RedirectResponse
    {
        $item = new MenuItem($this->attributes($request));
        $item->sort_order = $this->nextPosition($item->parent_id);
        $item->save();
        $this->menu->flush();

        return to_route('admin.menu.index')->with('success', __('admin.menu.created'));
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin.menu.form', [
            'item' => $menuItem,
            'parents' => $this->parents($menuItem),
        ]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->fill($this->attributes($request));

        if ($menuItem->isDirty('parent_id')) {
            $menuItem->sort_order = $this->nextPosition($menuItem->parent_id);
        }

        $menuItem->save();
        $this->menu->flush();

        return to_route('admin.menu.index')->with('success', __('admin.menu.updated'));
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();
        $this->menu->flush();

        return to_route('admin.menu.index')->with('success', __('admin.menu.deleted'));
    }

    public function reorder(MenuOrderRequest $request): Response
    {
        $rows = collect($request->validated('items'))->values();
        $items = MenuItem::query()->whereKey($rows->pluck('id'))->get()->keyBy('id');

        abort_unless($items->pluck('parent_id')->unique()->count() === 1, Response::HTTP_UNPROCESSABLE_ENTITY);

        DB::transaction(function () use ($rows, $items): void {
            foreach ($rows as $position => $row) {
                $items->get((int) $row['id'])->update(['sort_order' => $position, 'is_visible' => $row['visible']]);
            }
        });

        $this->menu->flush();

        return response()->noContent();
    }

    public function reset(): RedirectResponse
    {
        $this->menu->reset();

        return to_route('admin.menu.index')->with('success', __('admin.menu.restored'));
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(MenuItemRequest $request): array
    {
        $data = $request->validated();

        return [
            'parent_id' => $data['parent_id'] ?? null,
            'page' => $data['type'] === 'page' ? $data['page'] : null,
            'url' => $data['type'] === 'url' ? $data['url'] : null,
            'label' => $data['label'] ?? null,
            'is_visible' => $data['is_visible'],
        ];
    }

    private function nextPosition(?int $parentId): int
    {
        $last = MenuItem::query()->where('parent_id', $parentId)->max('sort_order');

        return $last === null ? 0 : $last + 1;
    }

    /**
     * @return array<int, string>
     */
    private function parents(?MenuItem $except = null): array
    {
        return MenuItem::query()
            ->roots()
            ->when($except !== null, fn ($query) => $query->whereKeyNot($except->id))
            ->ordered()
            ->get()
            ->mapWithKeys(fn (MenuItem $item): array => [$item->id => $item->title()])
            ->all();
    }
}
