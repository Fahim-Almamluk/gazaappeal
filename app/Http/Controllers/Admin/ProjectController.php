<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Support\ImageStore;
use App\Support\ListSort;
use App\Support\SpreadsheetExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ProjectController extends Controller
{
    private const string FOLDER = 'projects';

    public function __construct(private readonly ImageStore $images) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = $request->integer('status') ?: null;
        $priority = in_array($request->query('priority'), ['urgent', 'regular'], true) ? $request->query('priority') : null;
        $projects = $this->filtered($request)->paginate(config('appeal.per_page'))->withQueryString();

        return view('admin.projects.index', compact('projects', 'search', 'status', 'priority'));
    }

    public function export(Request $request, SpreadsheetExport $export): StreamedResponse
    {
        return $export->download('projects', __('admin.projects.title'), [
            '#', ...array_map(fn (string $key): string => __("admin.export.columns.{$key}"), ['title', 'summary', 'status', 'goal', 'raised', 'progress', 'urgent', 'published', 'order']),
        ], $this->filtered($request), fn (Project $project): array => [
            $project->id,
            $project->title,
            $project->summary,
            $project->status->name,
            $project->goal_amount === null ? null : (float) $project->goal_amount,
            (float) $project->raised_amount,
            $project->progress(),
            $export->yesNo($project->is_urgent),
            $export->yesNo($project->is_published),
            $project->sort_order,
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $search = $request->string('q')->trim()->toString();

        $query = Project::query()
            ->with('status')
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->when($request->integer('status') ?: null, fn ($query, int $status) => $query->where('status_id', $status))
            ->when(
                in_array($request->query('priority'), ['urgent', 'regular'], true),
                fn ($query) => $query->where('is_urgent', $request->query('priority') === 'urgent'),
            )
            ->visibility($request->query('visibility'))
            ->ordered();

        return ListSort::apply($query, $request, ['raised_amount']);
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['is_published' => true, 'sort_order' => 0]),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = new Project($request->safe()->except('image'));
        $project->image = $this->images->replace(null, $request->file('image'), self::FOLDER);
        $project->save();

        return to_route('admin.projects.index')->with('success', __('admin.projects.created'));
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.form', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->fill($request->safe()->except('image'));
        $project->image = $this->images->replace($project->image, $request->file('image'), self::FOLDER);
        $project->save();

        return to_route('admin.projects.index')->with('success', __('admin.projects.updated'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return to_route('admin.projects.index')->with('success', __('admin.projects.deleted'));
    }
}
