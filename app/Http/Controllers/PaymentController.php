<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function recordParticipantPayment(Request $request, Participant $participant): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'reference_no' => ['nullable', 'string', 'max:255'],
        ]);

        $participant = $this->paymentService->recordParticipantPayment(
            $participant,
            (float) $validated['amount'],
            [
                'reference_no' => $validated['reference_no'] ?? null,
                'status' => 'captured',
                'at' => now()->toDateTimeString(),
            ]
        );

        return response()->json([
            'message' => 'Payment recorded.',
            'participant' => $participant,
        ]);
    }

    public function mockGatewayCallback(Request $request): JsonResponse
    {
        return response()->json([
            'ok' => true,
            'payload' => $request->all(),
        ]);
    }
}
