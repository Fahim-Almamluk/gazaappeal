<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DonationMethodType;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\Publishable;
use App\Models\Concerns\Sortable;
use Database\Factories\DonationMethodFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['type', 'name', 'description', 'url', 'bank_details', 'is_published', 'sort_order'])]
final class DonationMethod extends Model
{
    use HasFactory;
    use HasTranslations;
    use LogsActivity;
    use Publishable;
    use SoftDeletes;
    use Sortable;

    /**
     * @var list<string>
     */
    protected array $translatable = ['name', 'description'];

    public const array BANK_FIELDS = ['bank_name', 'beneficiary', 'branch', 'address', 'account_number', 'swift_code', 'iban'];

    public const array LOCALIZED_BANK_FIELDS = ['bank_name', 'beneficiary', 'branch', 'address'];

    protected function casts(): array
    {
        return [
            'type' => DonationMethodType::class,
            'bank_details' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isBankTransfer(): bool
    {
        return $this->type === DonationMethodType::BankTransfer;
    }

    public function bankDetail(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $details = $this->bank_details ?? [];

        if ($locale !== config('app.fallback_locale') && in_array($field, self::LOCALIZED_BANK_FIELDS, true) && filled($details["{$field}_{$locale}"] ?? null)) {
            return $details["{$field}_{$locale}"];
        }

        return $details[$field] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public function filledBankDetails(): array
    {
        return array_filter(collect(self::BANK_FIELDS)->mapWithKeys(fn (string $field): array => [$field => $this->bankDetail($field)])->all());
    }

    protected static function newFactory(): DonationMethodFactory
    {
        return DonationMethodFactory::new();
    }
}
