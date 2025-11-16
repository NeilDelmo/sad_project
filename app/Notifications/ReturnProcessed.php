<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReturnProcessed extends Notification
{
    use Queueable;

    public function __construct(public Rental $rental) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'return_processed',
            'title' => 'Your rental return was processed',
            'message' => 'We have processed your rental return' . ($this->rental->late_fee > 0 ? (', late fee: ₱' . number_format($this->rental->late_fee, 2)) : '.'),
            'rental_id' => $this->rental->id,
            'action_url' => route('rentals.myrentals'),
            'returned_at' => optional($this->rental->returned_at)->toDateTimeString(),
            'late_fee' => $this->rental->late_fee,
        ];
    }
}
