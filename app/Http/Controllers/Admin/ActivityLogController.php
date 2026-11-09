<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $user = $request->integer('user') ?: null;
        $subject = in_array($request->query('subject'), ActivityLog::SUBJECTS, true) ? $request->query('subject') : null;
        $event = in_array($request->query('event'), ActivityLog::EVENTS, true) ? $request->query('event') : null;

        $logs = ActivityLog::query()
            ->with('user:id,name')
            ->when($search !== '', fn ($query) => $query->whereLike('subject_label', "%{$search}%"))
            ->when($user, fn ($query, int $user) => $query->where('user_id', $user))
            ->when($subject, fn ($query, string $subject) => $query->where('subject_type', $subject))
            ->when($event, fn ($query, string $event) => $query->where('event', $event))
            ->latest('id')
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.activity.index', [
            'logs' => $logs,
            'search' => $search,
            'user' => $user,
            'subject' => $subject,
            'event' => $event,
            'users' => User::query()->whereIn('id', ActivityLog::query()->select('user_id'))->orderBy('name')->pluck('name', 'id')->all(),
        ]);
    }
}
