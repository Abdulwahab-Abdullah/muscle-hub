<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tips;
use App\Models\User;
use Carbon\Carbon;

class TipsSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // ===== نصائح عامة لكل المستخدمين =====
        $generalTips = [
            // Dashboard
            [
                'title' => 'Check Your Stats',
                'content' => 'Review your progress regularly to stay on track with your goals.',
                'type' => 'Motivation',
                'page' => 'dashboard',
            ],
            [
                'title' => 'Set Daily Goals',
                'content' => 'Small daily goals keep you motivated and consistent.',
                'type' => 'Motivation',
                'page' => 'dashboard',
            ],

            // Exercise
            [
                'title' => 'Warm Up Properly',
                'content' => 'Always warm up for 5-10 minutes to prevent injuries.',
                'type' => 'Workout',
                'page' => 'exercise',
            ],
            [
                'title' => 'Stay Consistent',
                'content' => 'Consistency is more important than intensity. Keep moving!',
                'type' => 'Workout',
                'page' => 'exercise',
            ],

            // My Workouts
            [
                'title' => 'Track Your Sets',
                'content' => 'Logging your sets helps you monitor progress and stay motivated.',
                'type' => 'Workout',
                'page' => 'my-workouts',
            ],
            [
                'title' => 'Focus on Form',
                'content' => 'Proper technique prevents injuries and improves results.',
                'type' => 'Workout',
                'page' => 'my-workouts',
            ],

            // Nutrition
            [
                'title' => 'Eat Balanced Meals',
                'content' => 'Combine carbs, proteins, and fats for sustained energy.',
                'type' => 'Nutrition',
                'page' => 'nutrition',
            ],
            [
                'title' => 'Stay Hydrated',
                'content' => 'Drinking enough water improves digestion and energy levels.',
                'type' => 'Nutrition',
                'page' => 'nutrition',
            ],

            // Calories
            [
                'title' => 'Monitor Your Intake',
                'content' => 'Tracking calories helps you understand your energy balance.',
                'type' => 'Nutrition',
                'page' => 'calories',
            ],
            [
                'title' => 'Healthy Swaps',
                'content' => 'Swap sugary drinks with water or tea to cut unnecessary calories.',
                'type' => 'Nutrition',
                'page' => 'calories',
            ],

            // Progress
            [
                'title' => 'Celebrate Small Wins',
                'content' => 'Every milestone counts. Reward yourself for consistency.',
                'type' => 'Motivation',
                'page' => 'progress',
            ],
            [
                'title' => 'Review Weekly Progress',
                'content' => 'Look back on your achievements and plan the week ahead.',
                'type' => 'Motivation',
                'page' => 'progress',
            ],
        ];

        // ===== نصائح خاصة لكل مستخدم =====
        $users = User::all();

        foreach ($users as $user) {
            $userTips = [
                [
                    'title' => "Hello {$user->name}, Track Your Sets",
                    'content' => 'Logging your sets helps you monitor progress and stay motivated.',
                    'type' => 'Workout',
                    'page' => 'my-workouts',
                    'user_id' => $user->id,
                ],
                [
                    'title' => "Hi {$user->name}, Focus on Form",
                    'content' => 'Proper technique prevents injuries and improves results.',
                    'type' => 'Workout',
                    'page' => 'my-workouts',
                    'user_id' => $user->id,
                ],
                [
                    'title' => "Hey {$user->name}, Celebrate Small Wins",
                    'content' => 'Reward yourself for every milestone you achieve!',
                    'type' => 'Motivation',
                    'page' => 'progress',
                    'user_id' => $user->id,
                ],
            ];

            // دمج النصائح العامة مع نصائح المستخدم
            $allTips = array_merge($generalTips, $userTips);

            // إضافة توقيت ظهور تدريجي لكل نصيحة
            foreach ($allTips as $index => $tip) {
                Tips::create([
                    'title' => $tip['title'],
                    'content' => $tip['content'],
                    'type' => $tip['type'],
                    'page' => $tip['page'],
                    'user_id' => $tip['user_id'] ?? null,
                    'visible_at' => $now->copy()->addMinutes($index),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
