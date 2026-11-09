<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'event', 'subject_type', 'subject_id', 'subject_label', 'changes'])]
final class ActivityLog extends Model
{
    use HasFactory;

    public const ?string UPDATED_AT = null;

    public const array EVENTS = ['created', 'updated', 'deleted'];

    public const array SUBJECTS = ['Project', 'Loss', 'Damage', 'Story', 'GalleryItem', 'Partner', 'Faq', 'DonationMethod', 'Lookup', 'Volunteer', 'Role', 'User'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
