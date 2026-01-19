<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workouts', function (Blueprint $table) {

            // حذف الأعمدة القديمة أولاً
            if (Schema::hasColumn('workouts', 'name')) {
                $table->dropColumn('name');
            }

            if (Schema::hasColumn('workouts', 'muscle_group')) {
                $table->dropColumn('muscle_group');
            }

            // إضافة exercise_id
            $table->foreignId('exercise_id')
                ->after('workout_plan_id')
                ->constrained()
                ->onDelete('cascade');

            // منع التكرار
            $table->unique(['workout_plan_id', 'exercise_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::table('workouts', function (Blueprint $table) {

            $table->dropUnique(['workout_plan_id', 'exercise_id', 'day']);

            $table->dropForeign(['exercise_id']);
            $table->dropColumn('exercise_id');

            // إعادة الأعمدة القديمة (في حال rollback)
            $table->string('name');
            $table->string('muscle_group');
        });
    }
};
