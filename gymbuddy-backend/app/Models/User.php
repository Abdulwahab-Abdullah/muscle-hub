<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory; // generated tested data for models
use Illuminate\Foundation\Auth\User as Authenticatable; // The base user class provided by Laravel for authentication
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|Achievement[] $achievements
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany achievements()
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'height',
        'weight',
        'avatar',
        'role',
    ];

    public function goal()
    {
        return $this->hasOne(UserGoal::class);
    }

    public function plan()
    {
        return $this->hasOne(WorkoutPlan::class);
    }

    public function nutritionPlan()
    {
        return $this->hasOne(NutritionPlan::class);
    }

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function tips()
    {
        return $this->hasMany(Tips::class);
    }

    public function aiChatHistory()
    {
        return $this->hasMany(AiChatHistory::class);
    }

    public function workouts()
    {
        return $this->hasManyThrough(Workout::class, WorkoutPlan::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function unlockedAchievements()
    {
        return $this->achievements()->whereNotNull('unlocked_at');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
            'height' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }
}
