<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistributionResource extends JsonResource
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
            'slaughtering_id' => $this->slaughtering_id,
            'recipient_name' => $this->recipient_name,
            'recipient_type' => $this->recipient_type,
            'package_count' => $this->package_count,
            'status' => $this->status,
            'delivered_at' => $this->delivered_at,
            'handled_by' => $this->handled_by,
            'slaughtering' => $this->whenLoaded('slaughtering', fn () => [
                'id' => $this->slaughtering->id,
                'date' => $this->slaughtering->date,
                'location' => $this->slaughtering->location,
                'distribution_status' => $this->slaughtering->distribution_status,
                'animal' => $this->slaughtering->animal ? [
                    'id' => $this->slaughtering->animal->id,
                    'type' => $this->slaughtering->animal->type,
                    'weight' => $this->slaughtering->animal->weight,
                ] : null,
            ]),
        ];
    }
}
