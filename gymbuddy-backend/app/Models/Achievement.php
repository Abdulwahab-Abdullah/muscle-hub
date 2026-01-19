<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'title',
        'description',
        'icon',
        'category',
        'target',
        'is_daily',
    ];

    protected $casts = [
        'is_daily' => 'boolean',
    ];

    /* ================= Relations ================= */

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
