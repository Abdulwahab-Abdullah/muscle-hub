<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkoutPlan;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WorkoutPlanResource;

class WorkoutPlanController extends Controller
{
    // ✅ جلب جميع الخطط
    public function index()
    {
        $user = Auth::user();

        $plans = WorkoutPlan::with('workouts.exercise')
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->get();

        return response()->json([
            'success' => true,
            'data' => WorkoutPlanResource::collection($plans)
        ]);
    }

    // ✅ إنشاء خطة جديدة
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'workouts' => 'nullable|array',
            'workouts.*.exercise_id' => 'required_with:workouts|exists:exercises,id',
            'workouts.*.sets' => 'required_with:workouts|integer|min:1',
            'workouts.*.reps' => 'required_with:workouts|integer|min:1',
            'workouts.*.weight' => 'nullable|numeric|min:0',
            'workouts.*.day' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        if (!empty($validated['workouts']) && $duplicate = $this->hasDuplicateWorkouts($validated['workouts'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تكرار نفس التمرين في نفس اليوم',
                'duplicate' => $duplicate
            ], 422);
        }

        $plan = WorkoutPlan::create([
            'user_id' => $user->id,
            'plan_name' => $validated['plan_name'],
        ]);

        if (!empty($validated['workouts'])) {
            $this->createWorkouts($plan, $validated['workouts']);
        }

        return response()->json([
            'success' => true,
            'data' => $plan->load('workouts')
        ]);
    }

    // ✅ عرض خطة واحدة
    public function show($id)
    {
        $plan = WorkoutPlan::with('workouts.exercise')->findOrFail($id);

        if (!$this->authorizeUser($plan->user_id)) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new WorkoutPlanResource($plan)
        ]);
    }

    // ✅ تحديث الخطة
    public function update(Request $request, $id)
    {
        $plan = WorkoutPlan::findOrFail($id);

        if (!$this->authorizeUser($plan->user_id)) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $validated = $request->validate([
            'plan_name' => 'required|string|max:255',
            'workouts' => 'nullable|array',
            'workouts.*.exercise_id' => 'required_with:workouts|exists:exercises,id',
            'workouts.*.sets' => 'required_with:workouts|integer|min:1',
            'workouts.*.reps' => 'required_with:workouts|integer|min:1',
            'workouts.*.weight' => 'nullable|numeric|min:0',
            'workouts.*.day' => 'nullable|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        if (!empty($validated['workouts']) && $duplicate = $this->hasDuplicateWorkouts($validated['workouts'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تكرار نفس التمرين في نفس اليوم',
                'duplicate' => $duplicate
            ], 422);
        }

        $plan->update([
            'plan_name' => $validated['plan_name']
        ]);

        if (!empty($validated['workouts'])) {
            $plan->workouts()->delete();
            $this->createWorkouts($plan, $validated['workouts']);
        }

        return response()->json([
            'success' => true,
            'data' => $plan->load('workouts')
        ]);
    }

    // ✅ حذف الخطة (الآدمن أو صاحب الحساب)
    public function destroy($id)
    {
        $plan = WorkoutPlan::findOrFail($id);

        if (!$this->authorizeUser($plan->user_id)) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.workout_plan_deleted')
        ]);
    }

    // ================= HELPERS =================

    private function authorizeUser($userId)
    {
        return Auth::user()->role === 'admin' || Auth::id() === $userId;
    }

    private function hasDuplicateWorkouts($workouts)
    {
        $seen = [];
        foreach ($workouts as $w) {
            $key = $w['exercise_id'] . '-' . $w['day'];
            if (in_array($key, $seen)) return $key;
            $seen[] = $key;
        }
        return false;
    }

    private function createWorkouts($plan, $workouts)
    {
        // إنشاء كل التمارين دفعة واحدة لتقليل الاستعلامات
        $insertData = array_map(fn($w) => [
            'workout_plan_id' => $plan->id,
            'exercise_id' => $w['exercise_id'],
            'sets' => $w['sets'],
            'reps' => $w['reps'],
            'weight' => $w['weight'],
            'day' => $w['day'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $workouts);

        Workout::insert($insertData);
    }
}
