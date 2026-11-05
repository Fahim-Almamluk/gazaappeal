<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MessageState;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $state = MessageState::tryFrom($request->string('state')->toString());

        $messages = ContactMessage::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->when($state, fn ($query) => $query->inState($state), fn ($query) => $query->whereNull('archived_at'))
            ->latest()
            ->paginate(config('appeal.per_page'))
            ->withQueryString();

        return view('admin.messages.index', compact('messages', 'search', 'state'));
    }

    public function show(ContactMessage $message): View
    {
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return view('admin.messages.show', compact('message'));
    }

    public function archive(ContactMessage $message): RedirectResponse
    {
        $message->forceFill(['archived_at' => now()])->save();

        return to_route('admin.messages.index')->with('success', __('admin.messages.archived'));
    }

    public function restore(ContactMessage $message): RedirectResponse
    {
        $message->forceFill(['archived_at' => null])->save();

        return to_route('admin.messages.show', $message)->with('success', __('admin.messages.restored'));
    }
}
