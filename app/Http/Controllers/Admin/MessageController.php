<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\MessageState;
use App\Enums\Period;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\ListSort;
use App\Support\SpreadsheetExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $state = MessageState::tryFrom($request->string('state')->toString());
        $period = Period::tryFrom($request->integer('period'));

        $messages = $this->filtered($request)->paginate(config('appeal.per_page'))->withQueryString();

        return view('admin.messages.index', compact('messages', 'search', 'state', 'period'));
    }

    public function export(Request $request, SpreadsheetExport $export): StreamedResponse
    {
        return $export->download('messages', __('admin.messages.title'), [
            '#', ...array_map(fn (string $key): string => __("admin.export.columns.{$key}"), ['name', 'email', 'phone', 'organization', 'message', 'state', 'received']),
        ], $this->filtered($request), fn (ContactMessage $message): array => [
            $message->id,
            $message->name,
            $message->email,
            $message->phone,
            $message->organization,
            $message->body,
            $message->state()->label(),
            $message->created_at->format('Y-m-d H:i'),
        ]);
    }

    private function filtered(Request $request): Builder
    {
        $search = $request->string('q')->trim()->toString();

        $query = ContactMessage::query()
            ->when($search !== '', fn ($query) => $query->where(
                fn ($inner) => $inner->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->when(
                MessageState::tryFrom($request->string('state')->toString()),
                fn ($query, MessageState $state) => $query->inState($state),
                fn ($query) => $query->whereNull('archived_at'),
            )
            ->when(Period::tryFrom($request->integer('period')), fn ($query, Period $period) => $query->where('created_at', '>=', $period->since()))
            ->latest();

        return ListSort::apply($query, $request, ['name', 'created_at']);
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
