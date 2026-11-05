<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\VolunteerExpertise;
use App\Enums\VolunteerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VolunteerReviewRequest;
use App\Models\Volunteer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class VolunteerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = VolunteerStatus::tryFrom($request->integer('status'));
        $expertise = VolunteerExpertise::tryFrom($request->integer('expertise'));

        $volunteers = Volunteer::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($expertise, fn ($query) => $query->where('expertise', $expertise))
            ->latest()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.volunteers.index', compact('volunteers', 'search', 'status', 'expertise'));
    }

    public function show(Volunteer $volunteer): View
    {
        return view('admin.volunteers.show', [
            'volunteer' => $volunteer->load('reviewer:id,name'),
        ]);
    }

    public function update(VolunteerReviewRequest $request, Volunteer $volunteer): RedirectResponse
    {
        $volunteer->forceFill([
            ...$request->validated(),
            'reviewed_by' => $request->user()->id,
        ])->save();

        return to_route('admin.volunteers.show', $volunteer)->with('success', __('admin.volunteers.updated'));
    }
}
