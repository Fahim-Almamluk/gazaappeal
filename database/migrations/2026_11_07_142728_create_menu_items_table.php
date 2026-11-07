<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('page', 60)->nullable();
            $table->string('url')->nullable();
            $table->text('label')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
        });

        DB::table('menu_items')->insert(
            collect(config('navigation.defaults'))
                ->values()
                ->map(fn (string $page, int $position): array => ['page' => $page, 'sort_order' => $position])
                ->all(),
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
