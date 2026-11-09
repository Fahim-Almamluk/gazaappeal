<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Support\ActivityRecorder;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (self $model) => ActivityRecorder::record('created', $model));
        static::updated(fn (self $model) => ActivityRecorder::record('updated', $model, array_keys($model->getChanges())));
        static::deleted(fn (self $model) => ActivityRecorder::record('deleted', $model));
    }
}
