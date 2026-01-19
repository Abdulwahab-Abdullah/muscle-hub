<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class AuthController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $info = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'age' => $user->age,
            'height' => $user->height,
            'weight' => $user->weight,
            'sex' => $user->sex,
            'photo' => $user->avatar
                ? asset('storage/' . $user->avatar)
                : null,
        ];


        return response()->json($info);
    }

    public function uploadAvatar(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // حذف الصورة القديمة إن وجدت
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // حفظ الصورة الجديدة
        $path = $request->file('avatar')->store('avatars', 'public');

        // حفظ المسار في DB
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    // في AuthController.php
    public function deleteAvatar(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar deleted successfully'
        ]);
    }

    // تسجيل مستخدم جديد
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $cookie = cookie(
            'auth_token',
            $token,
            0, // session cookie
            '/',
            null,
            false,
            true,
            false,
            'lax'
        );

        return response()->json([
            'message' => __('messages.user_registered'),
            'user' => $user,
            'token' => $token
        ], 201)->withCookie($cookie);
    }

    // تسجيل دخول
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        $rememberMe = $validated['remember_me'] ?? false;

        $token = $user->createToken(
            'auth_token',
            ['*'],
            $rememberMe ? Carbon::now()->addDays(30) : null
        )->plainTextToken;

        $minutes = $rememberMe ? 60 * 24 * 30 : 0;

        $cookie = cookie(
            'auth_token',
            $token,
            $minutes,
            '/',
            null,
            false,
            true,
            false,
            'lax'
        );

        return response()->json([
            'message' => __('messages.login_success'),
            'user' => $user,
            'token' => $token
        ])->withCookie($cookie);
    }

    // تحديث الملف الشخصي مع Rate Limiting
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Rate Limiting: 5 محاولات كل 10 دقائق
        $key = 'update-profile:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => 'Too many update attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
                'retry_after' => $seconds
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        RateLimiter::hit($key, 600);

        // Validation مع قواعد صارمة
        $validated = $request->validate([
            'age' => 'nullable|integer|min:14|max:100',
            'height' => 'nullable|numeric|min:50|max:300',
            'weight' => 'nullable|numeric|min:20|max:300',
            'sex' => 'nullable|in:male,female',
        ]);

        // تحديث البيانات فقط إذا كانت مختلفة
        $hasChanges = false;

        if (isset($validated['age']) && $user->age !== (int)$validated['age']) {
            $user->age = $validated['age'];
            $hasChanges = true;
        }

        if (isset($validated['height']) && $user->height !== (float)$validated['height']) {
            $user->height = $validated['height'];
            $hasChanges = true;
        }

        if (isset($validated['weight']) && $user->weight !== (float)$validated['weight']) {
            $user->weight = $validated['weight'];
            $hasChanges = true;
        }
        if (isset($validated['sex']) && $user->sex !== $validated['sex']) {
            $user->sex = $validated['sex'];
            $hasChanges = true;
        }

        // حفظ فقط إذا كان في تغيير
        if ($hasChanges) {
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'No changes detected',
            'user' => $user
        ]);
    }
    // في AuthController.php
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    // تسجيل خروج
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        $cookie = Cookie::forget('auth_token');

        // امسح من localStorage كمان
        return response()->json([
            'message' => __('messages.logout_success')
        ])->withCookie($cookie);
    }
}
