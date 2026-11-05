<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 190)->unique();
            $table->string('summary', 300);
            $table->text('body');
            $table->string('image')->nullable();
            $table->decimal('goal_amount', 12, 2)->nullable();
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('status');
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('losses', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->decimal('figure', 14, 2);
            $table->string('unit', 40);
            $table->text('description');
            $table->string('icon', 40);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 160);
            $table->string('slug', 190)->unique();
            $table->string('summary', 300);
            $table->text('body');
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('caption', 160);
            $table->string('image');
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('donation_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type');
            $table->string('name', 120);
            $table->string('description', 300)->nullable();
            $table->string('url')->nullable();
            $table->json('bank_details')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 80)->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('donation_methods');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('losses');
        Schema::dropIfExists('projects');
    }
};
