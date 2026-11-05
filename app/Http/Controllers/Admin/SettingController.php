<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SettingController extends Controller
{
    public function __construct(private readonly SiteSettings $settings) {}

    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => $this->settings->all()]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $this->settings->update($request->validated());

        return to_route('admin.settings.edit')->with('success', __('admin.settings.updated'));
    }
}
