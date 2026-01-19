<?php

namespace App\Http\Controllers;

use App\Models\UserGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserGoalController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $goal = UserGoal::where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'goal' => $goal,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $validatedGoalData = $request->validate([
            'goal_type' => 'required|string|in:loss,maintain,gain',
            'activity_level' => 'nullable|string',
            'target' => 'nullable|numeric|min:1',
            'calories' => 'nullable|numeric|min:0',
            'start_weight' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
        ]);

        // جلب الهدف الحالي إذا موجود
        $goal = UserGoal::where('user_id', $user->id)->first();

        if ($goal) {
            // تحديث الهدف فقط
            $goal->update($validatedGoalData);
        } else {
            // إنشاء هدف جديد + تخزين start_weight و start_date تلقائي إذا ما أرسل
            $validatedGoalData['start_weight'] = $validatedGoalData['start_weight'] ?? $user->weight;
            $validatedGoalData['start_date'] = $validatedGoalData['start_date'] ?? now()->toDateString();
            $goal = UserGoal::create(array_merge(
                ['user_id' => $user->id],
                $validatedGoalData
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Goal saved successfully',
            'goal' => $goal,
        ]);
    }
}
