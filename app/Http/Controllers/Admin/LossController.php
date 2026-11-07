<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LossRequest;
use App\Models\Loss;
use App\Support\SpreadsheetExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class LossController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $losses = $this->filtered($request)->paginate(config('appeal.per_page'))->withQueryString();

        return view('admin.losses.index', compact('losses', 'search'));
    }

    public function export(Request $request, SpreadsheetExport $export): StreamedResponse
    {
        return $export->download('losses', __('admin.losses.title'), [
            '#', ...array_map(fn (string $key): string => __("admin.export.columns.{$key}"), ['title', 'figure', 'unit', 'description', 'icon', 'published', 'order']),
        ], $this->filtered($request), fn (Loss $loss): array => [
            $loss->id,
            $loss->title,
            (float) $loss->figure,
            $loss->unit,
            $loss->description,
            $loss->icon->label(),
            $export->yesNo($loss->is_published),
            $loss->sort_order,
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $search = $request->string('q')->trim()->toString();

        return Loss::query()
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->visibility($request->query('visibility'))
            ->ordered();
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
