<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->string('governorate', 20);
            $table->string('category', 20);
            $table->string('item', 120);
            $table->unsignedBigInteger('amount');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['governorate', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damages');
    }
};
