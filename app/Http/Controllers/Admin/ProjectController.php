<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Support\ImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProjectController extends Controller
{
    private const string FOLDER = 'projects';

    public function __construct(private readonly ImageStore $images) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $status = ProjectStatus::tryFrom($request->integer('status'));

        $projects = Project::query()
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->visibility($request->query('visibility'))
            ->ordered()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.projects.index', compact('projects', 'search', 'status'));
    }

    public function create(): View
    {
        return view('admin.projects.form', [
            'project' => new Project(['status' => ProjectStatus::Active, 'is_published' => true, 'sort_order' => 0]),
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
