<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $table = 'meals';

    protected $fillable = [
        'nutrition_plan_id',
        'name',
        'type',
        'calories',
        'protein',
        'carbs',
        'fat',
        'quantity',
        'source',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $timestamps = true;

    public function nutritionPlan()
    {
        return $this->belongsTo(NutritionPlan::class);
    }
}
