<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->integer('external_id')->unique();
            $table->string('name');
            $table->text('instructions');
            $table->string('thumbnail_url')->nullable();
            $table->string('video_url')->nullable();
            $table->foreignId('area_id')->constrained('areas');
            $table->foreignId('category_id')->constrained('categories');
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('ingredient_meal', function (Blueprint $table) {
            $table->foreignId('meal_id')->constrained('meals');
            $table->foreignId('ingredient_id')->constrained('ingredients');
            $table->string('measure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
        Schema::dropIfExists('ingredient_meal');
    }
};
