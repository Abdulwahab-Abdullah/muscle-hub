<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExercisesSeeder extends Seeder
{

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // تعطيل الفوريجن كي
        DB::table('exercises')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // إعادة تفعيل الفوريجن كي


        $exercises = [
            // Legs
            ['name' => 'Leg Press', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Leg-Press.jpg"],
            ['name' => 'Hack Squat', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Hack-Squat.jpg"],
            ['name' => 'Leg Extension', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Leg-Extensions.jpg"],
            ['name' => 'Leg Curl', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Curl-Leg.jpg"],
            ['name' => 'Lying Leg Curl', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Lying-Curl-Leg.jpg"],
            ['name' => 'Free Squat', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Squat.jpg"],
            ['name' => 'Portuguese Lunge', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Purgalian-Lungues.jpg"],
            ['name' => 'Smith Machine Squat', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Smith-Chair-Squat.jpg"],
            ['name' => 'Glutes Machine', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Glute.jpg"],
            ['name' => 'Deadlift', 'muscle_group' => 'Legs', 'image' => "http://127.0.0.1:8000/photos/Deadlift.jpg"],

            // Back
            ['name' => 'Lat Pulldown', 'muscle_group' => 'Back', 'image' => "http://127.0.0.1:8000/photos/Lat-Pulldwon.jpg"],
            ['name' => 'Seated Cable Row Close Grip', 'muscle_group' => 'Back', 'image' => "http://127.0.0.1:8000/photos/Cable-Seated-Row.jpg"],
            ['name' => 'T-Bar Row', 'muscle_group' => 'Back', 'image' => "http://127.0.0.1:8000/photos/T-Bar.jpg"],
            ['name' => 'Seated T-Bar Row', 'muscle_group' => 'Back', 'image' => "http://127.0.0.1:8000/photos/Seated-Row-Machine-T-Bar.jpg"],
            ['name' => 'Seated Row Machine', 'muscle_group' => 'Back', 'image' => "http://127.0.0.1:8000/photos/Seated-Row-Machine.jpg"],

            // Chest
            ['name' => 'Barbell Bench Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Barbell-Bench-Press.jpg"],
            ['name' => 'Dumbbell Bench Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Dumbbell-Bench-Press.jpg"],
            ['name' => 'Incline Barbell Bench Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Incline-Barbell.jpg"],
            ['name' => 'Incline Dumbbell Bench Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Incline-Dumbbell-Press.jpg"],
            ['name' => 'Incline Machine Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Incline-Press.jpg"],
            ['name' => 'Flat Machine Press', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Flat-Press.jpg"],
            ['name' => 'Chest Fly Machine', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Chest-Fly-Machine.jpg"],
            ['name' => 'Dumbbell Fly', 'muscle_group' => 'Chest', 'image' => "http://127.0.0.1:8000/photos/Dumbblell-Fly.jpg"],

            // Shoulders
            ['name' => 'Rear Delt Cross', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Rear-Delts-Cross.jpg"],
            ['name' => 'Face Pull', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Face-Pull.jpg"],
            ['name' => 'Dumbbell Lateral Raise', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Lateral-Raises-Dumbbell.jpg"],
            ['name' => 'Cable Lateral Raise', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Lateral-Raises-Cable.jpg"],
            ['name' => 'Dumbbell Press', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Dumbbell-Press.jpg"],
            ['name' => 'Machine Press', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Machine-Press.jpg"],
            ['name' => 'Smith Press', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Smith-Press.jpg"],
            ['name' => 'Shrugs', 'muscle_group' => 'Shoulders', 'image' => "http://127.0.0.1:8000/photos/Shrug.jpg"],

            // Biceps
            ['name' => 'Zigzag Curl', 'muscle_group' => 'Biceps', 'image' => "http://127.0.0.1:8000/photos/ZigZag.jpg"],
            ['name' => 'Dumbbell Curl', 'muscle_group' => 'Biceps', 'image' => "http://127.0.0.1:8000/photos/Dumbbell-Curl.jpg"],
            ['name' => 'Hammer Curl', 'muscle_group' => 'Biceps', 'image' => "http://127.0.0.1:8000/photos/Hammer-Curl.jpg"],
            ['name' => 'Preacher Curl', 'muscle_group' => 'Biceps', 'image' => "http://127.0.0.1:8000/photos/Preacher-Curl.jpg"],
            ['name' => 'Cable Curl', 'muscle_group' => 'Biceps', 'image' => "http://127.0.0.1:8000/photos/Cable-Curl.jpg"],

            // Triceps
            ['name' => 'Tricep Dips', 'muscle_group' => 'Triceps', 'image' => "http://127.0.0.1:8000/photos/Dips.jpg"],
            ['name' => 'Skull Crushers', 'muscle_group' => 'Triceps', 'image' => "http://127.0.0.1:8000/photos/Skull.jpg"],
            ['name' => 'Cable Tricep Pushdown', 'muscle_group' => 'Triceps', 'image' => "http://127.0.0.1:8000/photos/Row-Pushdown.jpg"],
            ['name' => 'Overhead Tricep Extension', 'muscle_group' => 'Triceps', 'image' => "http://127.0.0.1:8000/photos/Overhead-Cable.jpg"],

            // Forearms
            ['name' => 'Wrist Curl', 'muscle_group' => 'Forearms', 'image' => "http://127.0.0.1:8000/photos/Wrist-Curl.jpg"],
            ['name' => 'Reverse Wrist Curl', 'muscle_group' => 'Forearms', 'image' => "http://127.0.0.1:8000/photos/Wrist-Curl-Rev.jpg"],

            // Abs
            ['name' => 'Plank', 'muscle_group' => 'Abs', 'image' => "http://127.0.0.1:8000/photos/Planks.jpg"],
            ['name' => 'Hanging Leg Raises', 'muscle_group' => 'Abs', 'image' => "http://127.0.0.1:8000/photos/Abs-Mach.jpg"],
        ];

        DB::table('exercises')->insert($exercises);
    }
}
