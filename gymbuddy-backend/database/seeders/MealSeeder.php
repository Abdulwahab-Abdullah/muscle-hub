<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meal;

class MealSeeder extends Seeder
{
    public function run()
    {
        $meals = [
            // ---------------- Poultry ----------------
            ['name' => 'Chicken Breast (Skinless, Raw)', 'type' => 'Lunch', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6, 'source' => 'system'],
            ['name' => 'Chicken Breast (Grilled)', 'type' => 'Lunch', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6, 'source' => 'system'],
            ['name' => 'Chicken Thigh (With Skin, Raw)', 'type' => 'Dinner', 'calories' => 209, 'protein' => 19, 'carbs' => 0, 'fat' => 14, 'source' => 'system'],
            ['name' => 'Chicken Thigh (Grilled)', 'type' => 'Dinner', 'calories' => 229, 'protein' => 25, 'carbs' => 0, 'fat' => 15, 'source' => 'system'],
            ['name' => 'Turkey Breast (Raw)', 'type' => 'Lunch', 'calories' => 135, 'protein' => 30, 'carbs' => 0, 'fat' => 1, 'source' => 'system'],
            ['name' => 'Turkey Breast (Grilled)', 'type' => 'Lunch', 'calories' => 135, 'protein' => 30, 'carbs' => 0, 'fat' => 1, 'source' => 'system'],

            // ---------------- Beef ----------------
            ['name' => 'Beef Steak (Raw, 100g)', 'type' => 'Dinner', 'calories' => 250, 'protein' => 26, 'carbs' => 0, 'fat' => 17, 'source' => 'system'],
            ['name' => 'Beef Steak (Grilled)', 'type' => 'Dinner', 'calories' => 271, 'protein' => 30, 'carbs' => 0, 'fat' => 18, 'source' => 'system'],
            ['name' => 'Ground Beef 80% Lean (Raw)', 'type' => 'Lunch', 'calories' => 254, 'protein' => 17, 'carbs' => 0, 'fat' => 20, 'source' => 'system'],
            ['name' => 'Ground Beef 80% Lean (Cooked)', 'type' => 'Lunch', 'calories' => 332, 'protein' => 25, 'carbs' => 0, 'fat' => 25, 'source' => 'system'],

            // ---------------- Fish ----------------
            ['name' => 'Salmon (Raw)', 'type' => 'Lunch', 'calories' => 208, 'protein' => 20, 'carbs' => 0, 'fat' => 13, 'source' => 'system'],
            ['name' => 'Salmon (Grilled)', 'type' => 'Lunch', 'calories' => 233, 'protein' => 25, 'carbs' => 0, 'fat' => 14, 'source' => 'system'],
            ['name' => 'Tuna (Raw)', 'type' => 'Lunch', 'calories' => 132, 'protein' => 28, 'carbs' => 0, 'fat' => 1, 'source' => 'system'],
            ['name' => 'Tuna (Grilled)', 'type' => 'Lunch', 'calories' => 132, 'protein' => 28, 'carbs' => 0, 'fat' => 1, 'source' => 'system'],

            // ---------------- Eggs ----------------
            ['name' => 'Eggs (Whole, Raw)', 'type' => 'Breakfast', 'calories' => 143, 'protein' => 13, 'carbs' => 1.1, 'fat' => 9.5, 'source' => 'system'],
            ['name' => 'Eggs (Boiled)', 'type' => 'Breakfast', 'calories' => 155, 'protein' => 13, 'carbs' => 1.1, 'fat' => 11, 'source' => 'system'],
            ['name' => 'Egg Whites', 'type' => 'Breakfast', 'calories' => 52, 'protein' => 11, 'carbs' => 0.7, 'fat' => 0.2, 'source' => 'system'],

            // ---------------- Grains ----------------
            ['name' => 'Brown Rice (Cooked)', 'type' => 'Lunch', 'calories' => 112, 'protein' => 2.3, 'carbs' => 23, 'fat' => 0.8, 'source' => 'system'],
            ['name' => 'White Rice (Cooked)', 'type' => 'Lunch', 'calories' => 130, 'protein' => 2.4, 'carbs' => 28, 'fat' => 0.3, 'source' => 'system'],
            ['name' => 'Quinoa (Cooked)', 'type' => 'Lunch', 'calories' => 120, 'protein' => 4.1, 'carbs' => 21, 'fat' => 1.9, 'source' => 'system'],
            ['name' => 'Oats (Raw)', 'type' => 'Breakfast', 'calories' => 68, 'protein' => 2.4, 'carbs' => 12, 'fat' => 1.4, 'source' => 'system'],
            ['name' => 'Pasta (Cooked)', 'type' => 'Lunch', 'calories' => 131, 'protein' => 5, 'carbs' => 25, 'fat' => 1.1, 'source' => 'system'],
            ['name' => 'Sweet Potato (Raw)', 'type' => 'Dinner', 'calories' => 86, 'protein' => 1.6, 'carbs' => 20, 'fat' => 0.1, 'source' => 'system'],
            ['name' => 'Sweet Potato (Baked)', 'type' => 'Dinner', 'calories' => 90, 'protein' => 2, 'carbs' => 21, 'fat' => 0.2, 'source' => 'system'],

            // ---------------- Vegetables ----------------
            ['name' => 'Broccoli (Raw)', 'type' => 'Snack', 'calories' => 34, 'protein' => 2.8, 'carbs' => 6.6, 'fat' => 0.4, 'source' => 'system'],
            ['name' => 'Broccoli (Steamed)', 'type' => 'Snack', 'calories' => 35, 'protein' => 2.4, 'carbs' => 7.2, 'fat' => 0.4, 'source' => 'system'],
            ['name' => 'Spinach (Raw)', 'type' => 'Snack', 'calories' => 23, 'protein' => 2.9, 'carbs' => 3.6, 'fat' => 0.4, 'source' => 'system'],
            ['name' => 'Spinach (Cooked)', 'type' => 'Snack', 'calories' => 23, 'protein' => 3, 'carbs' => 3.6, 'fat' => 0.4, 'source' => 'system'],

            // ---------------- Fruits ----------------
            ['name' => 'Apple', 'type' => 'Snack', 'calories' => 52, 'protein' => 0.3, 'carbs' => 14, 'fat' => 0.2, 'source' => 'system'],
            ['name' => 'Banana', 'type' => 'Snack', 'calories' => 89, 'protein' => 1.1, 'carbs' => 23, 'fat' => 0.3, 'source' => 'system'],
            ['name' => 'Orange', 'type' => 'Snack', 'calories' => 47, 'protein' => 0.9, 'carbs' => 12, 'fat' => 0.1, 'source' => 'system'],
            ['name' => 'Blueberries', 'type' => 'Snack', 'calories' => 57, 'protein' => 0.7, 'carbs' => 14, 'fat' => 0.3, 'source' => 'system'],
            ['name' => 'Strawberries', 'type' => 'Snack', 'calories' => 32, 'protein' => 0.7, 'carbs' => 7.7, 'fat' => 0.3, 'source' => 'system'],

            // ---------------- Nuts & Oils ----------------
            ['name' => 'Almonds (Raw)', 'type' => 'Snack', 'calories' => 579, 'protein' => 21, 'carbs' => 22, 'fat' => 50, 'source' => 'system'],
            ['name' => 'Walnuts (Raw)', 'type' => 'Snack', 'calories' => 654, 'protein' => 15, 'carbs' => 14, 'fat' => 65, 'source' => 'system'],
            ['name' => 'Olive Oil', 'type' => 'Snack', 'calories' => 884, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'source' => 'system'],
            ['name' => 'Coconut Oil', 'type' => 'Snack', 'calories' => 862, 'protein' => 0, 'carbs' => 0, 'fat' => 100, 'source' => 'system'],

            // ---------------- Dairy ----------------
            ['name' => 'Whole Milk', 'type' => 'Breakfast', 'calories' => 61, 'protein' => 3.2, 'carbs' => 5, 'fat' => 3.3, 'source' => 'system'],
            ['name' => 'Skimmed Milk', 'type' => 'Breakfast', 'calories' => 34, 'protein' => 3.4, 'carbs' => 5, 'fat' => 0.1, 'source' => 'system'],
            ['name' => 'Greek Yogurt (Plain, Non-fat)', 'type' => 'Breakfast', 'calories' => 59, 'protein' => 10, 'carbs' => 3.6, 'fat' => 0.4, 'source' => 'system'],
            ['name' => 'Cheddar Cheese', 'type' => 'Snack', 'calories' => 403, 'protein' => 25, 'carbs' => 1.3, 'fat' => 33, 'source' => 'system'],

            // ---------------- Legumes ----------------
            ['name' => 'Lentils (Cooked)', 'type' => 'Lunch', 'calories' => 116, 'protein' => 9, 'carbs' => 20, 'fat' => 0.4, 'source' => 'system'],
            ['name' => 'Chickpeas (Cooked)', 'type' => 'Lunch', 'calories' => 164, 'protein' => 9, 'carbs' => 27, 'fat' => 2.6, 'source' => 'system'],
            ['name' => 'Black Beans (Cooked)', 'type' => 'Lunch', 'calories' => 132, 'protein' => 9, 'carbs' => 23, 'fat' => 0.5, 'source' => 'system'],

            // ---------------- Snacks ----------------
            ['name' => 'Protein Bar', 'type' => 'Snack', 'calories' => 200, 'protein' => 20, 'carbs' => 23, 'fat' => 5, 'source' => 'system'],
            ['name' => 'Peanut Butter (2 tbsp)', 'type' => 'Snack', 'calories' => 188, 'protein' => 8, 'carbs' => 6, 'fat' => 16, 'source' => 'system'],

            // ---------------- More Meals ----------------
            ['name' => 'Pork Chop (Raw)', 'type' => 'Dinner', 'calories' => 231, 'protein' => 25, 'carbs' => 0, 'fat' => 14, 'source' => 'system'],
            ['name' => 'Pork Chop (Grilled)', 'type' => 'Dinner', 'calories' => 242, 'protein' => 27, 'carbs' => 0, 'fat' => 14, 'source' => 'system'],
            ['name' => 'Shrimp (Raw)', 'type' => 'Lunch', 'calories' => 99, 'protein' => 24, 'carbs' => 0.2, 'fat' => 0.3, 'source' => 'system'],
            ['name' => 'Shrimp (Boiled)', 'type' => 'Lunch', 'calories' => 119, 'protein' => 24, 'carbs' => 0.2, 'fat' => 0.9, 'source' => 'system'],
        ];

        foreach ($meals as $meal) {
            Meal::create($meal);
        }
    }
}
