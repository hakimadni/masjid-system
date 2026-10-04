<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
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
            'type' => $this->type,
            'weight' => $this->weight,
            'price' => $this->price,
            'supplier' => $this->supplier,
            'location' => $this->location,
            'slaughter_type' => $this->slaughter_type,
            'vendor' => $this->vendor,
            'vendor_cost' => $this->vendor_cost,
            'pickup_schedule' => $this->pickup_schedule,
            'external_status' => $this->external_status,
            'status' => $this->status,
            'qr_token' => $this->qr_token,
            'participants_count' => $this->whenCounted('participants'),
            'participants' => $this->whenLoaded('participants', fn () =>
                $this->participants->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slot_number' => $p->slot_number,
                    'payment_status' => $p->payment_status,
                    'amount_paid' => $p->amount_paid,
                ])
            ),
            'slaughtering' => $this->whenLoaded('slaughtering', fn () => [
                'id' => $this->slaughtering->id,
                'date' => $this->slaughtering->date,
                'location' => $this->slaughtering->location,
                'meat_total_kg' => $this->slaughtering->meat_total_kg,
                'distribution_status' => $this->slaughtering->distribution_status,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
