<?php

namespace App\Services;

use App\Models\Participant;
use Illuminate\Support\Facades\Auth;

class PaymentService
{
    public function recordParticipantPayment(Participant $participant, float $amount, array $gatewayPayload = []): Participant
    {
        $newPaid = (float) $participant->amount_paid + $amount;
        $due = (float) $participant->amount_due;

        $status = 'unpaid';
        if ($newPaid >= $due && $due > 0) {
            $status = 'paid';
        } elseif ($newPaid > 0) {
            $status = 'partial';
        }

        $participant->update([
            'amount_paid' => $newPaid,
            'payment_status' => $status,
        ]);

        $participant->transactions()->create([
            'type' => 'payment',
            'amount' => $amount,
            'payment_status' => $status,
            'gateway_provider' => 'mock-gateway',
            'gateway_payload' => $gatewayPayload,
            'created_by' => Auth::id(),
        ]);

        return $participant->refresh();
    }
}
