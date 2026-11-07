<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const array TYPES = [
        'project_status' => [
            'table' => 'projects',
            'column' => 'status',
            'foreign' => 'status_id',
            'after' => 'raised_amount',
            'fallback' => 2,
            'items' => [
                1 => [1, ['ar' => 'قيد التخطيط', 'en' => 'Planned'], 'clock', 'neutral'],
                2 => [2, ['ar' => 'قيد التنفيذ', 'en' => 'In progress'], 'zap', 'info'],
                3 => [3, ['ar' => 'مكتمل', 'en' => 'Completed'], 'circle-check', 'success'],
            ],
        ],
        'damage_category' => [
            'table' => 'damages',
            'column' => 'category',
            'foreign' => 'category_id',
            'after' => 'governorate',
            'fallback' => 'operations',
            'items' => [
                4 => ['networks', ['ar' => 'الشبكات الكهربائية', 'en' => 'Electrical networks'], 'utility-pole', null],
                5 => ['buildings', ['ar' => 'مبانٍ ومخازن ومراكز', 'en' => 'Buildings, stores & centres'], 'building-2', null],
                6 => ['vehicles', ['ar' => 'سيارات وآليات ومولدات', 'en' => 'Vehicles, machinery & generators'], 'truck', null],
                7 => ['operations', ['ar' => 'رواتب ومصاريف تشغيلية', 'en' => 'Salaries & operating costs'], 'wallet', null],
            ],
        ],
        'volunteer_expertise' => [
            'table' => 'volunteers',
            'column' => 'expertise',
            'foreign' => 'expertise_id',
            'after' => 'country',
            'fallback' => 5,
            'items' => [
                8 => [1, ['ar' => 'البنية التحتية للطاقة الكهربائية', 'en' => 'Power infrastructure'], 'utility-pole', null],
                9 => [2, ['ar' => 'إدارة مشاريع الإغاثة والطوارئ', 'en' => 'Relief & emergency project management'], 'heart-handshake', null],
                10 => [3, ['ar' => 'إعادة التأهيل والإعمار في قطاع الطاقة', 'en' => 'Energy sector rehabilitation & reconstruction'], 'wrench', null],
                11 => [4, ['ar' => 'حلول الطاقة المتجددة والهجينة', 'en' => 'Renewable & hybrid energy solutions'], 'solar-panel', null],
                12 => [5, ['ar' => 'مجال آخر', 'en' => 'Other field'], 'lightbulb', null],
            ],
        ],
    ];

    public function up(): void
    {
        Schema::create('lookups', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);
            $table->text('name');
            $table->string('icon', 40)->nullable();
            $table->string('tone', 20)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'sort_order']);
        });

        $now = now();

        foreach (self::TYPES as $type => $definition) {
            $order = 0;

            foreach ($definition['items'] as $id => [, $name, $icon, $tone]) {
                DB::table('lookups')->insert([
                    'id' => $id,
                    'type' => $type,
                    'name' => json_encode($name, JSON_UNESCAPED_UNICODE),
                    'icon' => $icon,
                    'tone' => $tone,
                    'sort_order' => ++$order,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            Schema::table($definition['table'], function (Blueprint $table) use ($definition) {
                $table->unsignedBigInteger($definition['foreign'])->nullable()->after($definition['after']);
            });

            foreach ($definition['items'] as $id => [$value]) {
                DB::table($definition['table'])->where($definition['column'], $value)->update([$definition['foreign'] => $id]);
            }

            DB::table($definition['table'])->whereNull($definition['foreign'])->update([$definition['foreign'] => array_key_first($definition['items'])]);

            Schema::table($definition['table'], function (Blueprint $table) use ($definition) {
                if ($definition['table'] === 'damages') {
                    $table->dropIndex(['governorate', 'category']);
                }

                $table->dropColumn($definition['column']);
            });

            Schema::table($definition['table'], function (Blueprint $table) use ($definition) {
                $table->unsignedBigInteger($definition['foreign'])->nullable(false)->change();
                $table->foreign($definition['foreign'])->references('id')->on('lookups')->restrictOnDelete();
            });
        }

        Schema::table('damages', function (Blueprint $table) {
            $table->index(['governorate', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('damages', function (Blueprint $table) {
            $table->dropIndex(['governorate', 'category_id']);
        });

        foreach (self::TYPES as $definition) {
            Schema::table($definition['table'], function (Blueprint $table) use ($definition) {
                $column = is_int($definition['fallback'])
                    ? $table->unsignedTinyInteger($definition['column'])
                    : $table->string($definition['column'], 20);

                $column->default($definition['fallback'])->after($definition['after']);
            });

            foreach ($definition['items'] as $id => [$value]) {
                DB::table($definition['table'])->where($definition['foreign'], $id)->update([$definition['column'] => $value]);
            }

            Schema::table($definition['table'], function (Blueprint $table) use ($definition) {
                $table->dropConstrainedForeignId($definition['foreign']);
            });
        }

        Schema::table('damages', function (Blueprint $table) {
            $table->index(['governorate', 'category']);
        });

        Schema::dropIfExists('lookups');
    }
};
