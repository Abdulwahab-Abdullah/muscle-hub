<?php

namespace App\Http\Controllers;

use App\Models\NutritionPlan;
use App\Models\Meal;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NutritionPlanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $plan = NutritionPlan::where('user_id', $userId)
            ->with('meals')
            ->first();

        if (!$plan) {
            return response()->json([]);
        }

        return response()->json([$plan]);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'plan_name' => 'required|string',
            'goal' => 'required|in:cut,maintain,bulk',
            'daily_calories' => 'required|integer',
            'protein_target' => 'required|integer',
            'carbs_target' => 'required|integer',
            'fat_target' => 'required|integer',
            'meals' => 'required|array',
            'meals.*.name' => 'required|string',
            'meals.*.type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'meals.*.calories' => 'required|numeric',
            'meals.*.protein' => 'required|numeric',
            'meals.*.carbs' => 'required|numeric',
            'meals.*.fat' => 'required|numeric',
            'meals.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $plan = NutritionPlan::create([
                'user_id' => $userId,
                'plan_name' => $validated['plan_name'],
                'goal' => $validated['goal'],
                'daily_calories' => $validated['daily_calories'],
                'protein_target' => $validated['protein_target'],
                'carbs_target' => $validated['carbs_target'],
                'fat_target' => $validated['fat_target'],
            ]);

            foreach ($validated['meals'] as $mealData) {
                $plan->meals()->create([
                    'name' => $mealData['name'],
                    'type' => $mealData['type'],
                    'calories' => $mealData['calories'],
                    'protein' => $mealData['protein'],
                    'carbs' => $mealData['carbs'],
                    'fat' => $mealData['fat'],
                    'quantity' => $mealData['quantity'],
                    'source' => 'custom',
                ]);
            }

            // ✅ فحص إنجازات الوجبات
            $this->checkMealAchievements();

            DB::commit();

            return response()->json([
                'message' => __('messages.plan_saved'),
                'data' => $plan->load('meals')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => __('messages.failed_to_save_plan'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $plan = NutritionPlan::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $validated = $request->validate([
            'plan_name' => 'required|string',
            'goal' => 'required|in:cut,maintain,bulk',
            'daily_calories' => 'required|integer',
            'protein_target' => 'required|integer',
            'carbs_target' => 'required|integer',
            'fat_target' => 'required|integer',
            'meals' => 'required|array',
            'meals.*.id' => 'nullable|integer',
            'meals.*.name' => 'required|string',
            'meals.*.type' => 'required|in:Breakfast,Lunch,Dinner,Snack',
            'meals.*.calories' => 'required|numeric',
            'meals.*.protein' => 'required|numeric',
            'meals.*.carbs' => 'required|numeric',
            'meals.*.fat' => 'required|numeric',
            'meals.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // تحديث بيانات الخطة
            $plan->update([
                'plan_name' => $validated['plan_name'],
                'goal' => $validated['goal'],
                'daily_calories' => $validated['daily_calories'],
                'protein_target' => $validated['protein_target'],
                'carbs_target' => $validated['carbs_target'],
                'fat_target' => $validated['fat_target'],
            ]);

            $mealIds = collect($validated['meals'])->pluck('id')->filter()->toArray();

            // حذف أي وجبات تم حذفها في الواجهة
            $plan->meals()->whereNotIn('id', $mealIds)->delete();

            foreach ($validated['meals'] as $mealData) {
                if (!empty($mealData['id'])) {
                    // تحديث وجبة موجودة
                    $meal = Meal::where('id', $mealData['id'])
                        ->where('nutrition_plan_id', $plan->id)
                        ->first();

                    if ($meal) {
                        $meal->update([
                            'name' => $mealData['name'],
                            'type' => $mealData['type'],
                            'calories' => $mealData['calories'],
                            'protein' => $mealData['protein'],
                            'carbs' => $mealData['carbs'],
                            'fat' => $mealData['fat'],
                            'quantity' => $mealData['quantity'],
                        ]);
                    }
                } else {
                    // إضافة وجبة جديدة
                    $plan->meals()->create([
                        'name' => $mealData['name'],
                        'type' => $mealData['type'],
                        'calories' => $mealData['calories'],
                        'protein' => $mealData['protein'],
                        'carbs' => $mealData['carbs'],
                        'fat' => $mealData['fat'],
                        'quantity' => $mealData['quantity'],
                        'source' => 'custom',
                    ]);
                }
            }

            // ✅ فحص إنجازات الوجبات
            $this->checkMealAchievements();

            DB::commit();

            return response()->json([
                'message' => __('messages.plan_updated'),
                'data' => $plan->load('meals')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => __('messages.failed_to_update_plan'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMealsToday($id)
    {
        $userId = Auth::id();

        $plan = NutritionPlan::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $plan->meals()
            ->whereDate('created_at', today())
            ->delete();

        return response()->json([
            'message' => __('messages.meals_deleted')
        ]);
    }

    public function getMealsToday(Request $request)
    {
        $userId = Auth::id();

        $plan = NutritionPlan::where('user_id', $userId)->first();

        if (!$plan) {
            return response()->json(['meals' => []]);
        }

        $meals = Meal::where('nutrition_plan_id', $plan->id)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'type' => $meal->type,
                    'calories' => $meal->calories,
                    'protein' => $meal->protein,
                    'carbs' => $meal->carbs,
                    'fat' => $meal->fat,
                    'quantity' => $meal->quantity,
                    'source' => $meal->source,
                    'created_at' => $meal->created_at,
                ];
            });

        return response()->json([
            'meals' => $meals
        ]);
    }

    // ✅ دالة فحص إنجازات الوجبات
    private function checkMealAchievements()
    {
        $user = Auth::user();
        $plan = NutritionPlan::where('user_id', $user->id)->first();

        if (!$plan) return;

        // عدد الوجبات الكلي
        $totalMeals = Meal::where('nutrition_plan_id', $plan->id)->count();

        // فحص First Steps (وجبة واحدة)
        if ($totalMeals >= 1) {
            $this->unlockAchievement('first_steps');
        }

        // فحص Data Devotee (100 وجبة)
        if ($totalMeals >= 100) {
            $this->unlockAchievement('data_devotee');
        }
    }

    private function unlockAchievement($key)
    {
        $user = Auth::user();
        $achievement = Achievement::where('key', $key)->first();

        if ($achievement) {
            $userAchievement = $user->achievements()->where('achievement_id', $achievement->id)->first();

            if ($userAchievement && !$userAchievement->pivot->unlocked_at) {
                $userAchievement->pivot->unlocked_at = now();
                $userAchievement->pivot->save();
            }
        }
    }
}
