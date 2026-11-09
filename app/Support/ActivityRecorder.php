<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class ActivityRecorder
{
    private const array LABEL_FIELDS = ['title', 'name', 'item', 'caption', 'question', 'email'];

    private const array IGNORED_FIELDS = ['updated_at', 'created_at', 'deleted_at', 'password', 'remember_token', 'last_login_at'];

    /**
     * @param  list<string>|null  $fields
     */
    public static function record(string $event, Model $subject, ?array $fields = null): void
    {
        if (! Auth::check()) {
            return;
        }

        $fields = $fields === null ? null : array_values(array_diff($fields, self::IGNORED_FIELDS));

        if ($fields === []) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => app(Impersonation::class)->impersonatorId() ?? Auth::id(),
            'event' => $event,
            'subject_type' => class_basename($subject),
            'subject_id' => $subject->getKey(),
            'subject_label' => self::label($subject),
            'changes' => $fields,
        ]);
    }

    private static function label(Model $subject): ?string
    {
        $attributes = $subject->getAttributes();

        foreach (self::LABEL_FIELDS as $field) {
            if (array_key_exists($field, $attributes) && filled($value = $subject->getAttribute($field))) {
                return mb_strimwidth((string) $value, 0, 120, '…');
            }
        }

        return null;
    }
}
