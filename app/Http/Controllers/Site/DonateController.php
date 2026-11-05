<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\DonationMethod;
use Illuminate\View\View;

final class DonateController extends Controller
{
    public function __invoke(): View
    {
        return view('site.donate', [
            'methods' => DonationMethod::query()->published()->ordered()->get(),
        ]);
    }
}
