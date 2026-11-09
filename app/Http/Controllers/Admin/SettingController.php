<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingsRequest;
use App\Support\ImageStore;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SettingController extends Controller
{
    private const string FOLDER = 'branding';

    public function __construct(
        private readonly SiteSettings $settings,
        private readonly ImageStore $images,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => $this->settings->all()]);
    }

    public function update(SettingsRequest $request): RedirectResponse
    {
        $removals = collect(SettingsRequest::LOGOS)->map(fn (string $logo): string => "remove_{$logo}")->all();

        $this->settings->update([
            ...$request->safe()->except([...SettingsRequest::LOGOS, ...$removals]),
            ...$this->logos($request),
        ]);

        return to_route('admin.settings.edit')->with('success', __('admin.settings.updated'));
    }

    /**
     * @return array<string, ?string>
     */
    private function logos(SettingsRequest $request): array
    {
        $values = [];

        foreach (SettingsRequest::LOGOS as $logo) {
            $current = $this->settings->get($logo);

            if ($request->boolean("remove_{$logo}")) {
                $this->images->delete($current);
                $values[$logo] = null;
            } elseif ($request->hasFile($logo)) {
                $values[$logo] = $this->images->replace($current, $request->file($logo), self::FOLDER);
            }
        }

        return $values;
    }
}
