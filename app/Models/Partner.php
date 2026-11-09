<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'logo', 'website', 'is_published', 'sort_order'])]
final class Partner extends Model
{
    use HasFactory;
    use HasImage;
    use HasTranslations;
    use LogsActivity;
    use Publishable;
    use SoftDeletes;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function imageColumn(): string
    {
        return 'logo';
    }

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }
}
