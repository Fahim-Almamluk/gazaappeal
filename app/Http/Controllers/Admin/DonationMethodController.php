<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\DonationMethodType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DonationMethodRequest;
use App\Models\DonationMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DonationMethodController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $type = DonationMethodType::tryFrom($request->integer('type'));

        $methods = DonationMethod::query()
            ->when($search !== '', fn ($query) => $query->whereLike('name', "%{$search}%"))
            ->when($type, fn ($query, DonationMethodType $type) => $query->where('type', $type))
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.donation-methods.index', compact('methods', 'search', 'type'));
    }

    public function create(): View
    {
        return view('admin.donation-methods.form', [
            'method' => new DonationMethod(['type' => DonationMethodType::Link, 'is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(DonationMethodRequest $request): RedirectResponse
    {
        DonationMethod::query()->create($request->payload());

        return to_route('admin.donation-methods.index')->with('success', __('admin.donation_methods.created'));
    }

    public function edit(DonationMethod $donationMethod): View
    {
        return view('admin.donation-methods.form', ['method' => $donationMethod]);
    }

    public function update(DonationMethodRequest $request, DonationMethod $donationMethod): RedirectResponse
    {
        $donationMethod->update($request->payload());

        return to_route('admin.donation-methods.index')->with('success', __('admin.donation_methods.updated'));
    }

    public function destroy(DonationMethod $donationMethod): RedirectResponse
    {
        $donationMethod->delete();

        return to_route('admin.donation-methods.index')->with('success', __('admin.donation_methods.deleted'));
    }
}
