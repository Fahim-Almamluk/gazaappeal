<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SwitchLocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $previous = $request->session()->previousUrl();

        if ($previous === null || $previous === $request->fullUrl()) {
            return to_route('home');
        }

        return redirect()->to($previous);
    }
}
