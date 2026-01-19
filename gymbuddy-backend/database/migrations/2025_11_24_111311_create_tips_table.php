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
        Schema::create('tips', function (Blueprint $table) {
            $table->id();
            $table->string('title');   // عنوان النصيحة
            $table->text('content');   // محتوى النصيحة
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // اختياري، لو النصيحة خاصة بمستخدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tips');
    }
};
