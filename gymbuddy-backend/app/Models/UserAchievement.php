<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserAchievement extends Pivot
{
    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_id',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];
}
