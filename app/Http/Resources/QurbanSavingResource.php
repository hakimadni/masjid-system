<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QurbanSavingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $targetAmount = (float) $this->target_amount;
        $currentBalance = (float) $this->current_balance;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'target_amount' => $this->target_amount,
            'current_balance' => $this->current_balance,
            'remaining_amount' => max(0, $targetAmount - $currentBalance),
            'progress_percentage' => $targetAmount > 0 ? min(100, round(($currentBalance / $targetAmount) * 100, 2)) : 0,
            'status' => $this->status,
            'eligible_kambing' => $this->eligible_kambing,
            'eligible_sapi_share' => $this->eligible_sapi_share,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'participants' => $this->whenLoaded('participants', fn () =>
                $this->participants->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'animal_id' => $p->animal_id,
                    'amount_paid' => $p->amount_paid,
                    'amount_due' => $p->amount_due,
                    'payment_status' => $p->payment_status,
                ])
            ),
            'participants_count' => $this->whenLoaded('participants', fn () => $this->participants->count()),
            'created_at' => $this->created_at,
        ];
    }
}
