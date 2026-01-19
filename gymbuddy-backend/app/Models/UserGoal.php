<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class UserGoal extends Model
{
    use HasFactory;
    protected $table = 'user_goal';
    protected $fillable = [
        'user_id',
        'goal_type',
        'target',
        'progress',
        'calories',
        'activity_level',
        'start_weight',
        'start_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
