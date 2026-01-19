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
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_plan_id')->constrained('workout_plans')->onDelete('cascade'); // يربط التمرين بالخطة
            $table->string('name');          // اسم التمرين
            $table->integer('sets');         // عدد المجموعات
            $table->integer('reps');         // عدد التكرارات
            $table->string('day');           // اليوم المخصص للتمرين
            $table->string('muscle_group');  // العضلة المستهدفة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workouts');
    }
};
