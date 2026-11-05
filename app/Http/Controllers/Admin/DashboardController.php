<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MessageState;
use App\Enums\VolunteerStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $funding = Project::query()->published()->selectRaw('COALESCE(SUM(raised_amount), 0) as raised, COALESCE(SUM(goal_amount), 0) as goal')->first();

        return view('admin.dashboard', [
            'user' => $request->user(),
            'stats' => [
                'projects' => Project::query()->published()->count(),
                'raised' => (float) $funding->raised,
                'goal' => (float) $funding->goal,
                'volunteers' => Volunteer::query()->where('status', VolunteerStatus::New)->count(),
                'messages' => ContactMessage::query()->inState(MessageState::Unread)->count(),
            ],
            'projects' => Project::query()->published()->urgent()->ordered()->take(5)->get(),
            'volunteers' => Volunteer::query()->latest()->take(5)->get(),
            'messages' => ContactMessage::query()->inState(MessageState::Unread)->latest()->take(5)->get(),
        ]);
    }
}
