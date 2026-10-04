<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlaughteringResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'animal_id' => $this->animal_id,
            'date' => $this->date,
            'location' => $this->location,
            'cut_time' => $this->cut_time,
            'meat_total_kg' => $this->meat_total_kg,
            'distribution_status' => $this->distribution_status,
            'animal' => $this->whenLoaded('animal', fn () => [
                'id' => $this->animal->id,
                'type' => $this->animal->type,
                'weight' => $this->animal->weight,
                'price' => $this->animal->price,
                'supplier' => $this->animal->supplier,
                'status' => $this->animal->status,
                'participants_count' => $this->animal->participants_count ?? $this->animal->participants?->count() ?? 0,
            ]),
            'volunteers_count' => $this->whenCounted('volunteers'),
        ];
    }
}
