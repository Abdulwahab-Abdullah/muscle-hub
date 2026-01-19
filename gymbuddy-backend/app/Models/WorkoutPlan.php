<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class WorkoutPlan extends Model
{
    protected $fillable = ['user_id', 'plan_name'];

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
