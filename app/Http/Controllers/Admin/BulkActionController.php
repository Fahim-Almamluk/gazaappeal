<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkActionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

final class BulkActionController extends Controller
{
    public function __invoke(BulkActionRequest $request, string $module): RedirectResponse
    {
        $config = config("bulk.{$module}");
        $action = $request->validated('action');

        abort_if($action === 'delete' && $request->user()->cannot("{$config['module']}.delete"), 403);

        $items = $config['model']::query()->whereKey($request->validated('ids'))->get();

        DB::transaction(function () use ($items, $action): void {
            foreach ($items as $item) {
                if ($action === 'delete') {
                    $item->delete();

                    continue;
                }

                $item->forceFill(['is_published' => $action === 'publish'])->save();
            }
        });

        return to_route("admin.{$module}.index")->with('success', __("admin.bulk.done.{$action}", ['count' => $items->count()]));
    }
}
