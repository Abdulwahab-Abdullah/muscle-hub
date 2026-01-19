<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Http\Resources\ExerciseResource;

class ExerciseController extends Controller
{
    public function index()
    {
        // يرجع كل التمارين
        $exercises = Exercise::all();
        return ExerciseResource::collection($exercises);
    }
}
