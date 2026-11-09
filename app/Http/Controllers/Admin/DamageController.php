<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Governorate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DamageRequest;
use App\Models\Damage;
use App\Support\ListSort;
use App\Support\SpreadsheetExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DamageController extends Controller
{
    public function index(Request $request): View
    {
        $governorate = Governorate::tryFrom($request->string('governorate')->toString());
        $category = $request->integer('category') ?: null;
        $search = $request->string('q')->trim()->toString();
        $damages = $this->filtered($request)->paginate(config('appeal.per_page'))->withQueryString();

        return view('admin.damages.index', compact('damages', 'governorate', 'category', 'search'));
    }

    public function export(Request $request, SpreadsheetExport $export): StreamedResponse
    {
        return $export->download('damages', __('admin.damages.title'), [
            '#', ...array_map(fn (string $key): string => __("admin.export.columns.{$key}"), ['governorate', 'category', 'item', 'amount', 'order']),
        ], $this->filtered($request), fn (Damage $damage): array => [
            $damage->id,
            $damage->governorate->label(),
            $damage->category->name,
            $damage->item,
            $damage->amount,
            $damage->sort_order,
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $search = $request->string('q')->trim()->toString();

        $query = Damage::query()
            ->with('category')
            ->when($search !== '', fn ($query) => $query->whereLike('item', "%{$search}%"))
            ->when(Governorate::tryFrom($request->string('governorate')->toString()), fn ($query, Governorate $governorate) => $query->where('governorate', $governorate))
            ->when($request->integer('category') ?: null, fn ($query, int $category) => $query->where('category_id', $category))
            ->orderBy('governorate')
            ->orderBy('category_id')
            ->ordered();

        return ListSort::apply($query, $request, ['amount']);
    }

    public function create(): View
    {
        return view('admin.damages.form', [
            'damage' => new Damage(['sort_order' => 0]),
        ]);
    }

    public function store(DamageRequest $request): RedirectResponse
    {
        Damage::query()->create($request->validated());

        return to_route('admin.damages.index')->with('success', __('admin.damages.created'));
    }

    public function edit(Damage $damage): View
    {
        return view('admin.damages.form', compact('damage'));
    }

    public function update(DamageRequest $request, Damage $damage): RedirectResponse
    {
        $damage->update($request->validated());

        return to_route('admin.damages.index')->with('success', __('admin.damages.updated'));
    }

    public function destroy(Damage $damage): RedirectResponse
    {
        $damage->delete();

        return to_route('admin.damages.index')->with('success', __('admin.damages.deleted'));
    }
}
