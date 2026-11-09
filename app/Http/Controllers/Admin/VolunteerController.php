<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Period;
use App\Enums\VolunteerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VolunteerReviewRequest;
use App\Models\Volunteer;
use App\Support\ListSort;
use App\Support\SpreadsheetExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class VolunteerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = VolunteerStatus::tryFrom($request->integer('status'));
        $expertise = $request->integer('expertise') ?: null;
        $period = Period::tryFrom($request->integer('period'));

        $volunteers = $this->filtered($request)->paginate(config('appeal.per_page'))->withQueryString();

        return view('admin.volunteers.index', compact('volunteers', 'search', 'status', 'expertise', 'period'));
    }

    public function export(Request $request, SpreadsheetExport $export): StreamedResponse
    {
        return $export->download('volunteers', __('admin.volunteers.title'), [
            '#', ...array_map(fn (string $key): string => __("admin.export.columns.{$key}"), ['name', 'email', 'phone', 'country', 'expertise', 'experience', 'status', 'message', 'notes', 'received']),
        ], $this->filtered($request), fn (Volunteer $volunteer): array => [
            $volunteer->id,
            $volunteer->name,
            $volunteer->email,
            $volunteer->phone,
            $volunteer->country,
            $volunteer->expertise->name,
            $volunteer->experience_years,
            $volunteer->status->label(),
            $volunteer->message,
            $volunteer->notes,
            $volunteer->created_at->format('Y-m-d H:i'),
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $search = $request->string('q')->trim()->toString();

        $query = Volunteer::query()
            ->with('expertise')
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->when(VolunteerStatus::tryFrom($request->integer('status')), fn ($query, VolunteerStatus $status) => $query->where('status', $status))
            ->when($request->integer('expertise') ?: null, fn ($query, int $expertise) => $query->where('expertise_id', $expertise))
            ->when(Period::tryFrom($request->integer('period')), fn ($query, Period $period) => $query->where('created_at', '>=', $period->since()))
            ->latest();

        return ListSort::apply($query, $request, ['name', 'created_at']);
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
