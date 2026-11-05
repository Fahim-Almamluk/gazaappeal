<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageContentRequest;
use App\Support\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class PageContentController extends Controller
{
    public function __construct(private readonly PageContent $pages) {}

    public function edit(): View
    {
        return view('admin.pages.edit', ['pages' => $this->pages]);
    }

    public function update(PageContentRequest $request): RedirectResponse
    {
        $this->pages->update($request->validated());

        return to_route('admin.pages.edit')->with('success', __('admin.pages.updated'));
    }
}
