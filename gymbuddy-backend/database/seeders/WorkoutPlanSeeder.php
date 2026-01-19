<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkoutPlan;
use App\Models\Workout;

class WorkoutPlanSeeder extends Seeder
{
    public function run(): void
    {
        // مثال: إنشاء خطة تمرين واحدة للمستخدم رقم 1
        $plan = WorkoutPlan::create([
            'user_id' => 1,
            'plan_name' => 'Full Body'
        ]);

        $workouts = [
            ['exercise_id' => 1, 'sets' => 4, 'reps' => 8, 'day' => 'Monday'],
            ['exercise_id' => 2, 'sets' => 3, 'reps' => 10, 'day' => 'Monday'],
            ['exercise_id' => 3, 'sets' => 3, 'reps' => 12, 'day' => 'Monday'],
            ['exercise_id' => 4, 'sets' => 4, 'reps' => 6, 'day' => 'Wednesday'],
            ['exercise_id' => 5, 'sets' => 3, 'reps' => 10, 'day' => 'Wednesday'],
            ['exercise_id' => 6, 'sets' => 3, 'reps' => 12, 'day' => 'Wednesday'],
        ];

        foreach ($workouts as $w) {
            Workout::create(array_merge($w, ['workout_plan_id' => $plan->id]));
        }
    }
}
