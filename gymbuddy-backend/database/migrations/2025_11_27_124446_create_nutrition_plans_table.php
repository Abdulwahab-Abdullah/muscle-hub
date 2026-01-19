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
        Schema::create('nutrition_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // يربط الخطة بالمستخدم
            $table->string('plan_name'); // اسم الخطة
            $table->timestamps();
        });

        // جدول الوجبات
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nutrition_plan_id')->nullable()->constrained('nutrition_plans')->onDelete('cascade');
            $table->string('name'); // اسم الوجبة
            $table->string('type'); // فطور، غداء، عشاء، أو snack
            $table->integer('calories')->nullable();
            $table->integer('protein')->nullable();
            $table->integer('carbs')->nullable();
            $table->integer('fat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
        Schema::dropIfExists('nutrition_plans');
    }
};
