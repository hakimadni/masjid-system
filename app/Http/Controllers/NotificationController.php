<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Volunteer;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notificationService) {}

    public function paymentReminder(Participant $participant): JsonResponse
    {
        $this->notificationService->notifyPaymentReminder($participant);

        return response()->json(['message' => 'Payment reminder sent.']);
    }

    public function volunteerAssignment(Request $request, Volunteer $volunteer): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
        ]);

        $this->notificationService->notifyVolunteerAssignment($volunteer, $validated['message']);

        return response()->json(['message' => 'Volunteer assignment notification sent.']);
    }
}
