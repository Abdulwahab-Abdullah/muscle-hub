<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\UserGoal;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressController extends Controller
{
    // جلب كل التقدمات لمستخدم محدد
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        $progresses = $user->role === 'admin'
            ? Progress::orderBy('date', 'desc')->get()
            : Progress::where('user_id', $userId)->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $progresses
        ]);
    }

    // تسجيل أو تحديث التقدم اليومي
    public function store(Request $request)
    {
        $validated = $request->validate([
            'weight' => 'nullable|numeric|min:0',
            'muscle_mass' => 'nullable|numeric|min:0',
            'calories' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'workout_completed' => 'nullable|boolean',
        ]);

        $userId = Auth::id();
        $date = $validated['date'] ?? today();

        // جلب أول سجل Progress وهدف المستخدم
        $firstProgress = Progress::where('user_id', $userId)->orderBy('date', 'asc')->first();
        $goal = UserGoal::where('user_id', $userId)->orderBy('created_at', 'desc')->first();

        // إذا ما في أول سجل، اضف start_weight من الهدف أو وزن المستخدم
        if (!$firstProgress) {
            if ($goal && $goal->target) {
                $validated['start_weight'] = $goal->target;
            }
        }

        $progress = Progress::updateOrCreate(
            ['user_id' => $userId, 'date' => $date],
            $validated
        );

        // ✅ فحص وفتح الإنجازات بعد إضافة Progress
        $this->checkAndUnlockAchievements();

        return response()->json([
            'success' => true,
            'data' => $progress,
            'message' => 'Progress recorded successfully'
        ]);
    }

    // عرض سجل محدد
    public function show($id)
    {
        $progress = Progress::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $progress->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    // تحديث سجل موجود
    public function update(Request $request, $id)
    {
        $progress = Progress::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $progress->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'weight' => 'nullable|numeric|min:0',
            'muscle_mass' => 'nullable|numeric|min:0',
            'calories' => 'nullable|numeric|min:0',
            'workout_completed' => 'nullable|boolean',
        ]);

        $progress->update($validated);

        // ✅ فحص الإنجازات بعد التحديث
        $this->checkAndUnlockAchievements();

        return response()->json([
            'success' => true,
            'data' => $progress,
            'message' => 'Progress updated successfully'
        ]);
    }

    // حذف سجل
    public function destroy($id)
    {
        $progress = Progress::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $progress->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $progress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Progress deleted successfully'
        ]);
    }

    /**
     * حساب إحصائيات التقدم الكاملة
     */
    public function stats()
    {
        $userId = Auth::id();
        $user = Auth::user();

        $goal = UserGoal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$goal) {
            return response()->json([
                'success' => false,
                'message' => 'No goal found. Please set your goal first.'
            ], 404);
        }

        $firstProgress = Progress::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->first();

        $latestProgress = Progress::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->first();

        $startWeight = $firstProgress ? $firstProgress->weight : $user->weight;
        $currentWeight = $latestProgress ? $latestProgress->weight : $user->weight;
        $targetWeight = $goal->target;
        $goalType = $goal->goal_type; // loss, gain, maintain

        // تعديل حساب weightChange حسب الهدف
        if ($goalType === 'loss') {
            $weightChange = $startWeight - $currentWeight;
        } elseif ($goalType === 'gain') {
            $weightChange = $currentWeight - $startWeight;
        } else {
            $weightChange = $currentWeight - $startWeight;
        }

        $weightChangeAbs = abs($weightChange);
        $remainingWeight = abs($currentWeight - $targetWeight);

        $totalWeightToChange = abs($startWeight - $targetWeight);
        $progressPercentage = $totalWeightToChange > 0
            ? min(100, ($weightChangeAbs / $totalWeightToChange) * 100)
            : 0;

        $startDate = $firstProgress ? Carbon::parse($firstProgress->date) : $goal->created_at;
        $today = Carbon::today();
        $daysPassed = $startDate->diffInDays($today);
        $weeksPassed = max(1, $daysPassed / 7);

        $avgWeightChangePerWeek = $weightChange / $weeksPassed;

        $estimatedWeeksRemaining = abs($avgWeightChangePerWeek) > 0.1
            ? $remainingWeight / abs($avgWeightChangePerWeek)
            : null;

        $estimatedDaysRemaining = $estimatedWeeksRemaining ? round($estimatedWeeksRemaining * 7) : null;
        $estimatedMonthsRemaining = $estimatedWeeksRemaining ? round($estimatedWeeksRemaining / 4.33, 1) : null;
        $estimatedArrivalDate = $estimatedDaysRemaining ? $today->copy()->addDays($estimatedDaysRemaining)->format('Y-m-d') : null;

        $status = $this->getProgressStatus($goalType, $weightChange, $avgWeightChangePerWeek);

        $heightInMeters = $user->height / 100;
        $currentBMI = $heightInMeters > 0 ? round($currentWeight / ($heightInMeters ** 2), 1) : null;

        $recentProgress = Progress::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get()
            ->reverse()
            ->values();

        $totalRecords = Progress::where('user_id', $userId)->count();
        $completedWorkouts = Progress::where('user_id', $userId)
            ->where('workout_completed', true)
            ->count();
        $completionRate = $totalRecords > 0 ? round(($completedWorkouts / $totalRecords) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'goal_type' => $goalType,
                'start_weight' => round($startWeight, 1),
                'current_weight' => round($currentWeight, 1),
                'target_weight' => round($targetWeight, 1),
                'start_date' => $startDate->format('Y-m-d'),

                'weight_change' => round($weightChange, 1),
                'weight_change_abs' => round($weightChangeAbs, 1),
                'remaining_weight' => round($remainingWeight, 1),
                'progress_percentage' => round($progressPercentage, 1),

                'days_passed' => $daysPassed,
                'weeks_passed' => round($weeksPassed, 1),
                'avg_weight_change_per_week' => round($avgWeightChangePerWeek, 2),

                'estimated_weeks_remaining' => $estimatedWeeksRemaining ? round($estimatedWeeksRemaining, 1) : null,
                'estimated_days_remaining' => $estimatedDaysRemaining,
                'estimated_months_remaining' => $estimatedMonthsRemaining,
                'estimated_arrival_date' => $estimatedArrivalDate,

                'status' => $status,
                'current_bmi' => $currentBMI,
                'daily_workout_completion_rate' => $completionRate,
                'recent_progress' => $recentProgress,

                'daily_calories' => $goal->calories,
                'total_records' => $totalRecords,
            ]
        ]);
    }

    private function getProgressStatus($goalType, $weightChange, $avgPerWeek)
    {
        if ($goalType === 'maintain') {
            return abs($weightChange) < 2 ? 'on_track' : 'attention_needed';
        }

        if ($goalType === 'loss') {
            if ($weightChange > 0 && $avgPerWeek >= 0.3 && $avgPerWeek <= 1) return 'excellent';
            if ($weightChange > 0) return 'on_track';
            return 'attention_needed';
        }

        if ($goalType === 'gain') {
            if ($weightChange > 0 && $avgPerWeek >= 0.3 && $avgPerWeek <= 0.7) return 'excellent';
            if ($weightChange > 0) return 'on_track';
            return 'attention_needed';
        }

        return 'unknown';
    }

    // بيانات الرسم البياني الأسبوعي
    public function weeklyChart()
    {
        $userId = Auth::id();
        $twelveWeeksAgo = Carbon::today()->subWeeks(12);

        $progress = Progress::where('user_id', $userId)
            ->where('date', '>=', $twelveWeeksAgo)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-W');
            })
            ->map(function ($week) {
                return [
                    'week' => $week->first()->date,
                    'avg_weight' => round($week->avg('weight'), 1),
                    'avg_calories' => round($week->avg('calories'), 1),
                    'count' => $week->count(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    // ✅ دالة فحص وفتح الإنجازات
    private function checkAndUnlockAchievements()
    {
        $user = Auth::user();
        $goal = $user->goal;

        if (!$goal) return;

        // جلب عدد الـ Progress records
        $totalProgress = Progress::where('user_id', $user->id)->count();

        // جلب التغيير في الوزن
        $stats = $this->getStatsDataHelper();
        $weightLost = abs($stats['weight_change'] ?? 0);
        $progressPercentage = $stats['progress_percentage'] ?? 0;

        // فحص إنجاز Week Warrior (7 أيام)
        if ($totalProgress >= 7) {
            $this->unlockAchievement('week_warrior');
        }

        // فحص إنجاز Consistency King (30 يوم)
        if ($totalProgress >= 30) {
            $this->unlockAchievement('consistency_king');
        }

        // فحص إنجاز Goal Crusher (5 كجم)
        if ($weightLost >= 5) {
            $this->unlockAchievement('goal_crusher');
        }

        // فحص إنجاز Target Achieved (100% تقدم)
        if ($progressPercentage >= 100) {
            $this->unlockAchievement('target_reached');
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

    private function getStatsDataHelper()
    {
        $user = Auth::user();
        $goal = $user->goal;

        if (!$goal) return [];

        $progressRecords = Progress::where('user_id', $user->id)->orderBy('date', 'asc')->get();
        $currentWeight = $progressRecords->last()?->weight ?? $user->weight ?? 0;
        $startWeight = $goal->start_weight ?? $user->weight ?? 0;
        $targetWeight = $goal->target ?? 0;

        $weightChange = $currentWeight - $startWeight;

        $progressPercentage = 0;
        if ($goal->goal_type === 'loss' && $startWeight > $targetWeight) {
            $totalToLose = $startWeight - $targetWeight;
            $progressPercentage = ($totalToLose > 0) ? (($startWeight - $currentWeight) / $totalToLose) * 100 : 0;
        } elseif ($goal->goal_type === 'gain' && $targetWeight > $startWeight) {
            $totalToGain = $targetWeight - $startWeight;
            $progressPercentage = ($totalToGain > 0) ? (($currentWeight - $startWeight) / $totalToGain) * 100 : 0;
        }

        return [
            'weight_change' => $weightChange,
            'progress_percentage' => max(0, min(100, $progressPercentage)),
        ];
    }
}
