<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;

class MealController extends Controller
{
    // جلب كل الوجبات الجاهزة (system meals)
    public function index()
    {
        $meals = Meal::where('source', 'system')->get();
        return response()->json($meals);
    }
}
