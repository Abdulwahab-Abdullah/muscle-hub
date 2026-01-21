<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;



class AchievementController extends Controller
{
    // جلب كل الإنجازات للمستخدم الحالي
    public function index()
    {
        $user = Auth::user();

        $achievements = $user->achievements()->get();

        return response()->json([
            'success' => true,
            'achievements' => $achievements,
        ]);
    }

    // فتح إنجاز (unlock)
    public function unlock($key)
    {
        $user = Auth::user();
        $achievement = $user->achievements()->where('key', $key)->first();

        if (!$achievement) {
            return response()->json([
                'success' => false,
                'message' => __('messages.achievement_unlocked')
            ], 404);
        }

        $achievement->pivot->unlocked_at = now();
        $achievement->pivot->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.achievement_unlocked'),
            'achievement' => $achievement,
        ]);
    }

    // جلب الإنجازات اليومية فقط
    public function daily()
    {
        $user = Auth::user();

        $dailyAchievements = $user->achievements()
            ->where('is_daily', true)
            ->get();

        return response()->json([
            'success' => true,
            'daily_achievements' => $dailyAchievements,
        ]);
    }
}
