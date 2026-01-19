<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'muscle_group',
        'equipment',
        'difficulty',
        'image'
    ];

    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
}
