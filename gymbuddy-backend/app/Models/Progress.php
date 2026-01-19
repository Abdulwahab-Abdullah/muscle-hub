<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = [
        'user_id',
        'weight',
        'muscle_mass',
        'calories',
        'date',
        'workout_completed', // ✅ جديد
    ];

    protected $casts = [
        'workout_completed' => 'boolean', // ✅ تحويله لـ boolean تلقائي
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
