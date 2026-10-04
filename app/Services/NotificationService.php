<?php

namespace App\Services;

use App\Models\Participant;
use App\Models\User;
use App\Models\Volunteer;
use App\Notifications\PaymentReminderNotification;
use App\Notifications\VolunteerAssignmentNotification;

class NotificationService
{
    public function notifyPaymentReminder(Participant $participant): void
    {
        if ($participant->qurban_saving_id === null || $participant->qurbanSaving?->user === null) {
            return;
        }

        $participant->qurbanSaving->user->notify(new PaymentReminderNotification($participant));
    }

    public function notifyVolunteerAssignment(Volunteer $volunteer, string $message): void
    {
        if ($volunteer->user_id === null) {
            return;
        }

        $user = User::query()->find($volunteer->user_id);
        if ($user !== null) {
            $user->notify(new VolunteerAssignmentNotification($message));
        }
    }
}
