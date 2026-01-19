<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Tips extends Model
{
    protected $fillable = ['title', 'content', 'user_id', 'visible_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
