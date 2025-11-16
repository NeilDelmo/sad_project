<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentalApprovalExpiring extends Notification
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
            'type' => 'rental_approval_expiring',
            'title' => 'Rental Approval Expiring Soon',
            'message' => 'Your rental approval expires at ' . $this->rental->expires_at->format('M d, Y h:i A') . '. Please pick up your equipment before it is auto-cancelled.',
            'rental_id' => $this->rental->id,
            'action_url' => route('rentals.myrentals'),
            'expires_at' => optional($this->rental->expires_at)->toDateTimeString(),
        ];
    }
}
