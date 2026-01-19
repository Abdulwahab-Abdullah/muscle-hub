<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('user_goals', 'user_goal');
    }

    public function down()
    {
        Schema::rename('user_goal', 'user_goals');
    }
};
