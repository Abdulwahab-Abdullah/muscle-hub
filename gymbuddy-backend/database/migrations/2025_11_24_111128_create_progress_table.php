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
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // مرتبط بالمستخدم
            $table->float('weight')->nullable();       // الوزن
            $table->float('muscle_mass')->nullable();  // كتلة العضلات
            $table->float('calories')->nullable();    // السعرات اليومية
            $table->date('date');                      // تاريخ تسجيل التقدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
