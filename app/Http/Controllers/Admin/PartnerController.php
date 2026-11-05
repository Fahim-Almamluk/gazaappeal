<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PartnerRequest;
use App\Models\Partner;
use App\Support\ImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PartnerController extends Controller
{
    private const string FOLDER = 'partners';

    public function __construct(private readonly ImageStore $images) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $partners = Partner::query()
            ->when($search !== '', fn ($query) => $query->whereLike('name', "%{$search}%"))
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.partners.index', compact('partners', 'search'));
    }

    public function create(): View
    {
        return view('admin.partners.form', [
            'partner' => new Partner(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(PartnerRequest $request): RedirectResponse
    {
        $partner = new Partner($request->safe()->except('logo'));
        $partner->logo = $this->images->replace(null, $request->file('logo'), self::FOLDER);
        $partner->save();

        return to_route('admin.partners.index')->with('success', __('admin.partners.created'));
    }

    public function edit(Partner $partner): View
    {
        return view('admin.partners.form', compact('partner'));
    }

    public function update(PartnerRequest $request, Partner $partner): RedirectResponse
    {
        $partner->fill($request->safe()->except('logo'));
        $partner->logo = $this->images->replace($partner->logo, $request->file('logo'), self::FOLDER);
        $partner->save();

        return to_route('admin.partners.index')->with('success', __('admin.partners.updated'));
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $partner->delete();

        return to_route('admin.partners.index')->with('success', __('admin.partners.deleted'));
    }
}
