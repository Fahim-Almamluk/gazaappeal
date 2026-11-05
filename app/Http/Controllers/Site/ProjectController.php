<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;

final class ProjectController extends Controller
{
    public function index(): View
    {
        return view('site.projects.index', [
            'projects' => Project::query()->published()->ordered()->paginate(9),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        return view('site.projects.show', [
            'project' => $project,
            'stories' => $project->stories()->published()->latest()->take(3)->get(),
            'related' => Project::query()->published()->whereKeyNot($project->getKey())->ordered()->take(3)->get(),
        ]);
    }
}
