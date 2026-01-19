<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;
use App\Models\User;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'key' => 'first_steps',
                'title' => 'First Steps',
                'description' => 'Log your first meal',
                'icon' => 'fa-utensils',
                'category' => 'milestone',
                'target' => 1,
                'is_daily' => false,
            ],
            [
                'key' => 'week_warrior',
                'title' => 'Week Warrior',
                'description' => 'Track progress for 7 days',
                'icon' => 'fa-fire',
                'category' => 'streak',
                'target' => 7,
                'is_daily' => false,
            ],
            [
                'key' => 'consistency_king',
                'title' => 'Consistency King',
                'description' => '30 day tracking streak',
                'icon' => 'fa-crown',
                'category' => 'streak',
                'target' => 30,
                'is_daily' => false,
            ],
            [
                'key' => 'goal_crusher',
                'title' => 'Goal Crusher',
                'description' => 'Change 5kg from starting weight',
                'icon' => 'fa-dumbbell',
                'category' => 'goal',
                'target' => 5,
                'is_daily' => false,
            ],
            [
                'key' => 'data_devotee',
                'title' => 'Data Devotee',
                'description' => 'Log 100 meals',
                'icon' => 'fa-database',
                'category' => 'milestone',
                'target' => 100,
                'is_daily' => false,
            ],
            [
                'key' => 'target_reached',
                'title' => 'Target Achieved',
                'description' => 'Reach your target weight',
                'icon' => 'fa-bullseye',
                'category' => 'goal',
                'target' => null,
                'is_daily' => false,
            ],
        ];

        // أولاً، تأكد من أن الإنجازات موجودة في جدول الإنجازات
        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['key' => $achievement['key']], // تحقق من الـ key
                $achievement
            );
        }

        // ثم أضف الإنجازات لكل مستخدم (pivot)
        $users = User::all();

        foreach ($users as $user) {
            foreach ($achievements as $achievementData) {
                $achievement = Achievement::where('key', $achievementData['key'])->first();

                // إذا لم يكن الإنجاز موجودًا في pivot، أضفه
                if (!$user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                    $user->achievements()->attach($achievement->id, [
                        'unlocked_at' => null, // لم يتم فتحه بعد
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
