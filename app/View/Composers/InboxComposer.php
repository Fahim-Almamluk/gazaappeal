<?php

declare(strict_types=1);

namespace App\View\Composers;

use App\Enums\MessageState;
use App\Enums\VolunteerStatus;
use App\Models\ContactMessage;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class InboxComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();

        $view->with('inbox', [
            'volunteers' => $user?->hasPermission('volunteers.view') ? Volunteer::query()->where('status', VolunteerStatus::New)->count() : 0,
            'messages' => $user?->hasPermission('messages.view') ? ContactMessage::query()->inState(MessageState::Unread)->count() : 0,
        ]);
    }
}
