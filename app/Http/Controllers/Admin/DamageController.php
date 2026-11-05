<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DamageCategory;
use App\Enums\Governorate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DamageRequest;
use App\Models\Damage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DamageController extends Controller
{
    public function index(Request $request): View
    {
        $governorate = Governorate::tryFrom($request->string('governorate')->toString());
        $category = DamageCategory::tryFrom($request->string('category')->toString());

        $damages = Damage::query()
            ->when($governorate, fn ($query) => $query->where('governorate', $governorate))
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('governorate')
            ->orderBy('category')
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.damages.index', compact('damages', 'governorate', 'category'));
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
