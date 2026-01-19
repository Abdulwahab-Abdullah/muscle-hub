// database/migrations/xxxx_add_quantity_to_meals_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            // إضافة عمود الكمية
            $table->integer('quantity')->default(1)->after('fat');
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
