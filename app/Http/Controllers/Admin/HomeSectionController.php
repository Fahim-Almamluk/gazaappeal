<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomeSectionsRequest;
use App\Support\HomeSections;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class HomeSectionController extends Controller
{
    public function __construct(private readonly HomeSections $sections) {}

    public function edit(): View
    {
        return view('admin.home-sections.edit', ['sections' => $this->sections->all()]);
    }

    public function update(HomeSectionsRequest $request): Response
    {
        $this->sections->update($request->validated('items'));

        return response()->noContent();
    }

    public function reset(): RedirectResponse
    {
        $this->sections->reset();

        return to_route('admin.home-sections.edit')->with('success', __('admin.home_sections.restored'));
    }
}
