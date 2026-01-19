<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'exercise_id',
        'sets',
        'reps',
        'weight',
        'day',
    ];

    protected $casts = [
        'sets' => 'integer',
        'reps' => 'integer',
        'weight' => 'float',
    ];

    public function workoutPlan()
    {
        return $this->belongsTo(WorkoutPlan::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
