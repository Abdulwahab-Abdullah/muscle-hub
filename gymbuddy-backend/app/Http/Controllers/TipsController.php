<?php

namespace App\Http\Controllers;

use App\Models\Tips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TipsController extends Controller
{
    // جلب كل النصائح المتاحة للمستخدم حسب الوقت والنوع
    public function index(Request $request)
    {
        $userId = Auth::id();
        $now = now();
        $type = $request->query('type'); // مثال: ?type=Nutrition

        $tips = Tips::where(function ($query) use ($userId) {
            $query->whereNull('user_id')  // نصائح عامة
                ->orWhere('user_id', $userId); // نصائح خاصة بالمستخدم
        })
            ->where(function ($query) use ($now) {
                $query->whereNull('visible_at')
                    ->orWhere('visible_at', '<=', $now);
            })
            ->when($type, fn($query) => $query->where('type', $type))
            ->with('user')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tips
        ]);
    }

    // جلب نصيحة واحدة
    public function show($id)
    {
        $tip = Tips::with('user')->find($id);

        if (!$tip) {
            return response()->json(['success' => false, 'message' => __('messages.tip_not_found')], 404);
        }

        if ($tip->user_id && $tip->user_id != Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $tip
        ]);
    }

    // إنشاء نصيحة جديدة (Admin فقط)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'visible_at' => 'nullable|date',
            'type' => 'nullable|string|max:50',
        ]);

        $tip = Tips::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.tip_created'),
            'data' => $tip
        ], 201);
    }

    // تحديث نصيحة (Admin فقط)
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $tip = Tips::find($id);
        if (!$tip) {
            return response()->json(['success' => false, 'message' => __('messages.tip_not_found')], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'visible_at' => 'sometimes|date',
            'type' => 'sometimes|string|max:50',
        ]);

        $tip->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.tip_updated'),
            'data' => $tip
        ]);
    }

    // حذف نصيحة (Admin فقط)
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $tip = Tips::find($id);
        if (!$tip) {
            return response()->json(['success' => false, 'message' => __('messages.tip_not_found')], 404);
        }

        $tip->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.tip_deleted')
        ]);
    }
}
