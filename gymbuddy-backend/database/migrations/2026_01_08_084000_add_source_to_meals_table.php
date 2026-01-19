<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->enum('source', ['system', 'custom'])->default('custom')->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
