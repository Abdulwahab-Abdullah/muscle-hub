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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            // مثال: first_meal, week_warrior

            $table->string('title');
            // First Steps

            $table->text('description');
            // Log your first meal

            $table->string('icon')->nullable();
            // fa-solid fa-fire

            $table->string('category')->nullable();
            // meals, weight, consistency

            $table->integer('target')->nullable();
            // 7 أيام - 30 يوم - 100 وجبة - 5 كجم

            $table->boolean('is_daily')->default(false);
            // false = إنجاز دائم
            // true = يومي (غالبًا ما راح نحفظه)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
