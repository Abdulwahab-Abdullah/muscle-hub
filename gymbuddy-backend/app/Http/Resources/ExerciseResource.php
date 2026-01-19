<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'muscle' => $this->muscle_group,
            'image' => $this->image ?? 'https://via.placeholder.com/400x250',
            'sets' => $this->sets ?? null,
            'reps' => $this->reps ?? null,
        ];
    }
}
