<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoryRequest;
use App\Models\Project;
use App\Models\Story;
use App\Support\ImageStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class StoryController extends Controller
{
    private const string FOLDER = 'stories';

    public function __construct(private readonly ImageStore $images) {}

    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $project = $request->integer('project') ?: null;

        $stories = Story::query()
            ->with('project:id,title')
            ->when($search !== '', fn ($query) => $query->whereLike('title', "%{$search}%"))
            ->when($project, fn ($query, int $project) => $query->where('project_id', $project))
            ->visibility($request->query('visibility'))
            ->latest()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.stories.index', [
            'stories' => $stories,
            'search' => $search,
            'project' => $project,
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stories.form', [
            'story' => new Story(['is_published' => true]),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function store(StoryRequest $request): RedirectResponse
    {
        $story = new Story($request->safe()->except('image'));
        $story->image = $this->images->replace(null, $request->file('image'), self::FOLDER);
        $story->save();

        return to_route('admin.stories.index')->with('success', __('admin.stories.created'));
    }

    public function edit(Story $story): View
    {
        return view('admin.stories.form', [
            'story' => $story,
            'projects' => $this->projectOptions(),
        ]);
    }

    public function update(StoryRequest $request, Story $story): RedirectResponse
    {
        $story->fill($request->safe()->except('image'));
        $story->image = $this->images->replace($story->image, $request->file('image'), self::FOLDER);
        $story->save();

        return to_route('admin.stories.index')->with('success', __('admin.stories.updated'));
    }

    public function destroy(Story $story): RedirectResponse
    {
        $story->delete();

        return to_route('admin.stories.index')->with('success', __('admin.stories.deleted'));
    }

    private function projectOptions(): array
    {
        return Project::query()->ordered()->pluck('title', 'id')->all();
    }
}
