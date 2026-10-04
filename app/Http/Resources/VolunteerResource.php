<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'role_type' => $this->role_type,
            'qr_token' => $this->qr_token,
            'is_active' => $this->is_active,
            'availabilities' => $this->whenLoaded('availabilities'),
        ];
    }
}
