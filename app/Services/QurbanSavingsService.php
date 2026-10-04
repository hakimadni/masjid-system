<?php

namespace App\Services;

use App\Models\QurbanSaving;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QurbanSavingsService
{
    public const KAMBING_PRICE = 2500000;

    public const SAPI_SHARE_PRICE = 3000000;

    public function topup(QurbanSaving $saving, float $amount, ?string $notes = null): QurbanSaving
    {
        return DB::transaction(function () use ($saving, $amount, $notes): QurbanSaving {
            if ($saving->status === 'cancelled') {
                abort(422, 'Cancelled saving cannot receive top up.');
            }

            $saving->increment('current_balance', $amount);
            $saving->refresh();

            $this->refreshEligibility($saving);

            $saving->transactions()->create([
                'type' => 'topup',
                'amount' => $amount,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);

            return $saving;
        });
    }

    public function withdraw(QurbanSaving $saving, float $amount, ?string $notes = null): QurbanSaving
    {
        return DB::transaction(function () use ($saving, $amount, $notes): QurbanSaving {
            if ($saving->status === 'cancelled') {
                abort(422, 'Cancelled saving cannot be withdrawn.');
            }

            if ((float) $saving->current_balance < $amount) {
                abort(422, 'Insufficient balance.');
            }

            $saving->decrement('current_balance', $amount);
            $saving->refresh();

            $this->refreshEligibility($saving);

            $saving->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $amount,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);

            return $saving;
        });
    }

    public function refreshEligibility(QurbanSaving $saving): void
    {
        $balance = (float) $saving->current_balance;
        $target = (float) $saving->target_amount;

        $nextStatus = $saving->status === 'cancelled'
            ? 'cancelled'
            : ($balance >= $target ? 'completed' : 'active');

        $saving->update([
            'eligible_kambing' => $balance >= self::KAMBING_PRICE,
            'eligible_sapi_share' => $balance >= self::SAPI_SHARE_PRICE,
            'status' => $nextStatus,
        ]);
    }
}
