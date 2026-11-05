<?php

declare(strict_types=1);

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

final class FaqController extends Controller
{
    public function __invoke(): View
    {
        return view('site.faq', [
            'faqs' => Faq::query()->published()->ordered()->get(),
        ]);
    }
}
