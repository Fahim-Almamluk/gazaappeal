<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, array<string, array{0: int|null, 1: bool}>>
     */
    private const array COLUMNS = [
        'projects' => ['title' => [160, false], 'summary' => [300, false], 'body' => [null, false]],
        'losses' => ['title' => [160, false], 'unit' => [40, false], 'description' => [null, false]],
        'stories' => ['title' => [160, false], 'summary' => [300, false], 'body' => [null, false]],
        'partners' => ['name' => [120, false]],
        'faqs' => ['question' => [255, false], 'answer' => [null, false]],
        'gallery_items' => ['caption' => [160, false]],
        'donation_methods' => ['name' => [120, false], 'description' => [300, true]],
        'damages' => ['item' => [120, false]],
    ];

    private const string SOURCE_LOCALE = 'ar';

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column => [, $nullable]) {
                    $blueprint->text($column)->nullable($nullable)->change();
                }
            });

            $this->rewrite($table, array_keys($columns), fn (string $value): ?string => $this->decode($value) === null
                ? json_encode([self::SOURCE_LOCALE => $value], JSON_UNESCAPED_UNICODE)
                : null);
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            $this->rewrite($table, array_keys($columns), function (string $value): ?string {
                $translations = $this->decode($value);

                return $translations === null ? null : (string) ($translations[self::SOURCE_LOCALE] ?? reset($translations));
            });

            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column => [$length, $nullable]) {
                    ($length === null ? $blueprint->text($column) : $blueprint->string($column, $length))->nullable($nullable)->change();
                }
            });
        }
    }

    /**
     * @param  list<string>  $columns
     */
    private function rewrite(string $table, array $columns, Closure $transform): void
    {
        DB::table($table)->orderBy('id')->each(function (object $row) use ($table, $columns, $transform) {
            $values = collect($columns)
                ->filter(fn (string $column): bool => $row->{$column} !== null)
                ->map(fn (string $column): ?string => $transform((string) $row->{$column}))
                ->filter(fn (?string $value): bool => $value !== null)
                ->mapWithKeys(fn (string $value, int $index): array => [$columns[$index] => $value])
                ->all();

            if ($values !== []) {
                DB::table($table)->where('id', $row->id)->update($values);
            }
        });
    }

    /**
     * @return array<string, string>|null
     */
    private function decode(string $value): ?array
    {
        $decoded = json_decode($value, true);

        return is_array($decoded) && ! array_is_list($decoded) ? $decoded : null;
    }
};
