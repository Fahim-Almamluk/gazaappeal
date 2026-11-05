<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LossRequest;
use App\Models\Loss;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LossController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $losses = Loss::query()
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.losses.index', compact('losses', 'search'));
    }

    public function create(): View
    {
        return view('admin.losses.form', [
            'loss' => new Loss(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(LossRequest $request): RedirectResponse
    {
        Loss::query()->create($request->validated());

        return to_route('admin.losses.index')->with('success', __('admin.losses.created'));
    }

    public function edit(Loss $loss): View
    {
        return view('admin.losses.form', compact('loss'));
    }

    public function update(LossRequest $request, Loss $loss): RedirectResponse
    {
        $loss->update($request->validated());

        return to_route('admin.losses.index')->with('success', __('admin.losses.updated'));
    }

    public function destroy(Loss $loss): RedirectResponse
    {
        $loss->delete();

        return to_route('admin.losses.index')->with('success', __('admin.losses.deleted'));
    }
}
