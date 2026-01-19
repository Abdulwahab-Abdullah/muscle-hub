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
        Schema::table('user_goal', function (Blueprint $table) {
            $table->double('start_weight')->nullable()->after('target');
            $table->date('start_date')->nullable()->after('start_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_goal', function (Blueprint $table) {
            $table->dropColumn('start_weight');
            $table->dropColumn('start_date');
        });
    }
};
