<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentalApprovalCancelled extends Notification
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
            'type' => 'rental_approval_cancelled',
            'title' => 'Rental Approval Expired',
            'message' => 'Your rental approval expired and has been auto-cancelled. Please submit a new request if you still need the equipment.',
            'rental_id' => $this->rental->id,
            'action_url' => route('rentals.index'),
        ];
    }
}
