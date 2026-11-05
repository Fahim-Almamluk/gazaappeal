<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\Publishable;
use Database\Factories\StoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['project_id', 'title', 'summary', 'body', 'image', 'is_published'])]
final class Story extends Model
{
    use HasFactory;
    use HasImage;
    use HasTranslations;
    use Publishable;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected array $translatable = ['title', 'summary', 'body'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    protected static function newFactory(): StoryFactory
    {
        return StoryFactory::new();
    }
}
