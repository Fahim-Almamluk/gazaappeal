<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VolunteerStatus;
use Database\Factories\VolunteerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'phone', 'country', 'expertise_id', 'experience_years', 'message'])]
final class Volunteer extends Model
{
    use HasFactory;

    protected $attributes = [
        'status' => VolunteerStatus::New->value,
    ];

    protected function casts(): array
    {
        return [
            'status' => VolunteerStatus::class,
            'experience_years' => 'integer',
        ];
    }

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Lookup::class, 'expertise_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected static function newFactory(): VolunteerFactory
    {
        return VolunteerFactory::new();
    }
}
