<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $path = $request->path() === '/' ? '' : '/'.$request->path();
        $query = $request->getQueryString();

        return redirect('/'.app()->getLocale().$path.($query ? '?'.$query : ''));
    }
}
