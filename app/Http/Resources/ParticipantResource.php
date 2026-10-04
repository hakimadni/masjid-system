<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
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
            'qurban_saving_id' => $this->qurban_saving_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'amount_due' => $this->amount_due,
            'amount_paid' => $this->amount_paid,
            'payment_status' => $this->payment_status,
            'slot_number' => $this->slot_number,
            'animal' => $this->whenLoaded('animal', fn () => [
                'id' => $this->animal->id,
                'type' => $this->animal->type,
                'weight' => $this->animal->weight,
                'supplier' => $this->animal->supplier,
            ]),
            'qurban_saving' => $this->whenLoaded('qurbanSaving', [
                'id' => $this->qurbanSaving->id,
                'current_balance' => $this->qurbanSaving->current_balance,
                'status' => $this->qurbanSaving->status,
                'user' => [
                    'id' => $this->qurbanSaving->user->id,
                    'name' => $this->qurbanSaving->user->name,
                ],
            ]),
            'created_at' => $this->created_at,
        ];
    }
}