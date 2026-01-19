<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_name' => $this->plan_name,
            'workouts' => $this->workouts->map(function ($workout) {
                return [
                    'id' => $workout->id,
                    'exercise' => [
                        'id' => $workout->exercise->id,
                        'name' => $workout->exercise->name,
                        'muscle_group' => $workout->exercise->muscle_group,
                    ],
                    'sets' => $workout->sets,
                    'reps' => $workout->reps,
                    'weight' => $workout->weight,
                    'day' => $workout->day,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
