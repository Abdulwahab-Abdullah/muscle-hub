<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NutritionPlan extends Model
{
    protected $table = 'nutrition_plans';

    protected $fillable = [
        'user_id',
        'plan_name',
        'goal',
        'daily_calories',
        'protein_target',
        'carbs_target',
        'fat_target',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }
}
