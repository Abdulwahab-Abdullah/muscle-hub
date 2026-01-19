<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nutrition_plans', function (Blueprint $table) {
            $table->enum('goal', ['cut', 'maintain', 'bulk'])->after('plan_name')->default('maintain');
            $table->integer('daily_calories')->nullable()->after('goal');
            $table->integer('protein_target')->nullable()->after('daily_calories');
            $table->integer('carbs_target')->nullable()->after('protein_target');
            $table->integer('fat_target')->nullable()->after('carbs_target');
        });
    }

    public function down(): void
    {
        Schema::table('nutrition_plans', function (Blueprint $table) {
            $table->dropColumn(['goal', 'daily_calories', 'protein_target', 'carbs_target', 'fat_target']);
        });
    }
};
