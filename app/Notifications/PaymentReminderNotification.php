<?php

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Participant $participant) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_reminder',
            'participant_id' => $this->participant->id,
            'participant_name' => $this->participant->name,
            'amount_due' => $this->participant->amount_due,
            'amount_paid' => $this->participant->amount_paid,
            'message' => 'Pengingat pembayaran qurban Anda.',
        ];
    }
}
