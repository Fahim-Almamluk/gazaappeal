<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\LookupType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LookupRequest;
use App\Models\Lookup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LookupController extends Controller
{
    public function index(Request $request): View
    {
        $type = $this->type($request);
        $search = $request->string('q')->trim()->toString();

        $lookups = Lookup::query()
            ->ofType($type)
            ->withCount($type->usage().' as usage_count')
            ->when($search !== '', fn ($query) => $query->whereLike('name', "%{$search}%"))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        $counts = Lookup::query()->pluck('type')->countBy(fn (LookupType $lookupType): string => $lookupType->value);

        return view('admin.lookups.index', compact('lookups', 'type', 'search', 'counts'));
    }

    public function create(Request $request): View
    {
        $lookup = new Lookup(['is_active' => true, 'sort_order' => 0]);
        $lookup->type = $this->type($request);

        return view('admin.lookups.form', compact('lookup'));
    }

    public function store(LookupRequest $request): RedirectResponse
    {
        $lookup = new Lookup($request->safe()->except('type'));
        $lookup->type = LookupType::from($request->validated('type'));
        $lookup->save();

        return to_route('admin.lookups.index', ['type' => $lookup->type->value])->with('success', __('admin.lookups.created'));
    }

    public function edit(Lookup $lookup): View
    {
        return view('admin.lookups.form', compact('lookup'));
    }

    public function update(LookupRequest $request, Lookup $lookup): RedirectResponse
    {
        $lookup->update($request->validated());

        return to_route('admin.lookups.index', ['type' => $lookup->type->value])->with('success', __('admin.lookups.updated'));
    }

    public function destroy(Lookup $lookup): RedirectResponse
    {
        $usage = $lookup->usages()->count();

        if ($usage > 0) {
            return to_route('admin.lookups.index', ['type' => $lookup->type->value])
                ->with('error', trans_choice('admin.lookups.in_use', $usage, ['count' => $usage]));
        }

        $lookup->delete();

        return to_route('admin.lookups.index', ['type' => $lookup->type->value])->with('success', __('admin.lookups.deleted'));
    }

    private function type(Request $request): LookupType
    {
        return LookupType::tryFrom($request->string('type')->toString()) ?? LookupType::ProjectStatus;
    }
}
