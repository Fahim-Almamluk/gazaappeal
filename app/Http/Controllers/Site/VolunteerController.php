<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Enums\LookupType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\VolunteerRequest;
use App\Models\Lookup;
use App\Models\Volunteer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class VolunteerController extends Controller
{
    public function create(): View
    {
        return view('site.volunteer', [
            'expertises' => Lookup::query()->ofType(LookupType::VolunteerExpertise)->active()->ordered()->get(),
        ]);
    }

    public function store(VolunteerRequest $request): RedirectResponse
    {
        Volunteer::query()->create($request->safe()->except('website'));

        return to_route('volunteer.create')->with('success', __('site.volunteer.sent'));
    }
}
