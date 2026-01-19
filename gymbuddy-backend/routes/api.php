<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserGoalController;
use App\Http\Controllers\WorkoutPlanController;
use App\Http\Controllers\NutritionPlanController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\TipsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AchievementController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Public)
|--------------------------------------------------------------------------
*/

// Contact form submission
Route::post('/contact', [ContactController::class, 'store']);

Route::prefix('auth')->group(function () {

    // Register new user
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:5,60')
        ->name('auth.register');

    // Login user
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:10,5')
        ->name('auth.login');

    // Logout user
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('auth.logout');

    // Update user profile
    Route::post('profile', [AuthController::class, 'updateProfile'])
        ->middleware(['auth:sanctum', 'throttle:10,10'])
        ->name('auth.updateProfile');

    // في api.php تضيف:
    Route::post('change-password', [AuthController::class, 'changePassword'])
        ->middleware(['auth:sanctum', 'throttle:5,10'])
        ->name('auth.changePassword');

    // Get authenticated user info
    Route::get('user-info', [AuthController::class, 'index'])
        ->middleware('auth:sanctum')
        ->name('auth.userInfo');

    Route::middleware('auth:sanctum')->post(
        'upload-avatar',
        [AuthController::class, 'uploadAvatar']
    )->name(
        'auth.uploadAvatar'
    );

    Route::delete('delete-avatar', [AuthController::class, 'deleteAvatar'])
        ->middleware(['auth:sanctum'])
        ->name('auth.deleteAvatar');
});


/*
|--------------------------------------------------------------------------
| Protected Routes (Require Authentication)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Session
    |--------------------------------------------------------------------------
    */

    // Used by frontend to verify authentication
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    /*
    |--------------------------------------------------------------------------
    | Exercises
    |--------------------------------------------------------------------------
    */

    Route::get('/exercises', [ExerciseController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | User Goals
    |--------------------------------------------------------------------------
    */

    Route::prefix('goals')->name('goals.')->group(function () {
        Route::get('/', [UserGoalController::class, 'index'])->name('index');
        Route::post('/', [UserGoalController::class, 'store'])->name('store');
    });

    /*
    |--------------------------------------------------------------------------
    | Workout Plans
    */

    Route::prefix('workout-plans')->name('workout-plans.')->group(function () {
        Route::get('/', [WorkoutPlanController::class, 'index'])->name('index');
        Route::get('/{id}', [WorkoutPlanController::class, 'show'])->name('show');
        Route::post('/', [WorkoutPlanController::class, 'store'])->name('store');
        Route::put('/{id}', [WorkoutPlanController::class, 'update'])->name('update');
        Route::delete('/{id}', [WorkoutPlanController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Nutrition Plans
    |--------------------------------------------------------------------------
    */

    Route::prefix('nutrition-plans')->name('nutrition-plans.')->group(function () {

        // Get all nutrition plans
        Route::get('/', [NutritionPlanController::class, 'index'])->name('index');



        // Create new nutrition plan
        Route::post('/', [NutritionPlanController::class, 'store'])->name('store');

        // Update nutrition plan
        Route::put('/{id}', [NutritionPlanController::class, 'update'])->name('update');

        // Delete today's meals only
        Route::delete('/{id}/meals-today', [NutritionPlanController::class, 'deleteMealsToday'])
            ->name('meals-today.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Meals
    |--------------------------------------------------------------------------
    */

    // Get all meals (library / system meals)
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');

    // Get today's meals for the user
    Route::get('/meals/today', [NutritionPlanController::class, 'getMealsToday'])
        ->name('meals.today');

    /*
    |--------------------------------------------------------------------------
    | Progress Tracking
    |--------------------------------------------------------------------------
    */

    Route::prefix('progress')->name('progress.')->group(function () {
        Route::get('/', [ProgressController::class, 'index'])->name('index');
        Route::get('/stats', [ProgressController::class, 'stats'])->name('stats'); // ✅ NEW
        Route::get('/weekly-chart', [ProgressController::class, 'weeklyChart'])->name('weekly-chart'); // ✅ NEW
        Route::get('/{id}', [ProgressController::class, 'show'])->name('show');
        Route::post('/', [ProgressController::class, 'store'])->name('store');
        Route::put('/{id}', [ProgressController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProgressController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Tips & Advice
    |--------------------------------------------------------------------------
    */

    Route::prefix('tips')->name('tips.')->group(function () {
        Route::get('/', [TipsController::class, 'index'])->name('index');
        Route::get('/{id}', [TipsController::class, 'show'])->name('show');
        Route::post('/', [TipsController::class, 'store'])->name('store');
        Route::put('/{id}', [TipsController::class, 'update'])->name('update');
        Route::delete('/{id}', [TipsController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | AI Chat
    |--------------------------------------------------------------------------
    */

    Route::prefix('ai-chat')->name('ai-chat.')->group(function () {

        // Get chat history
        Route::get('/history', [ChatbotController::class, 'history'])
            ->name('history');

        // Send message to chatbot (rate limited)
        Route::post('/send', [ChatbotController::class, 'sendMessage'])
            ->middleware('throttle:30,1')
            ->name('send');

        // Clear chat history
        Route::post('/clear-history', [ChatbotController::class, 'clearHistory'])
            ->name('clearHistory');
    });

    /*--------------------------------------------------------------------------
    | Achievements
    |--------------------------------------------------------------------------*/

    Route::prefix('achievements')->name('achievements.')->group(function () {
        Route::get('/', [AchievementController::class, 'index'])->name('index');
        Route::post('/unlock/{key}', [AchievementController::class, 'unlock'])->name('unlock');
        Route::get('/daily', [AchievementController::class, 'daily'])->name('daily');
    });
});
