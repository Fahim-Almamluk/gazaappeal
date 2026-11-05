<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $faqs = Faq::query()
            ->when($search !== '', fn ($query) => $query->whereLike('question', "%{$search}%"))
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs', 'search'));
    }

    public function create(): View
    {
        return view('admin.faqs.form', [
            'faq' => new Faq(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(FaqRequest $request): RedirectResponse
    {
        Faq::query()->create($request->validated());

        return to_route('admin.faqs.index')->with('success', __('admin.faqs.created'));
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.form', compact('faq'));
    }

    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return to_route('admin.faqs.index')->with('success', __('admin.faqs.updated'));
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return to_route('admin.faqs.index')->with('success', __('admin.faqs.deleted'));
    }
}
