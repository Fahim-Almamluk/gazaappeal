<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class ContactController extends Controller
{
    public function create(): View
    {
        return view('site.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        ContactMessage::query()->create($request->safe()->except('website'));

        return to_route('contact.create')->with('success', __('site.contact.sent'));
    }
}
