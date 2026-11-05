<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Loss;
use Illuminate\View\View;

final class LossController extends Controller
{
    public function __invoke(): View
    {
        return view('site.losses', [
            'losses' => Loss::query()->published()->ordered()->get(),
        ]);
    }
}
