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
        $parts = [];
        if ($this->rental->late_fee > 0) {
            $parts[] = 'Late fee ₱' . number_format($this->rental->late_fee, 2);
        }
        if ($this->rental->damage_fee > 0) {
            $parts[] = 'Damage fee ₱' . number_format($this->rental->damage_fee, 2);
        } elseif ($this->rental->damage_fee_waived) {
            $parts[] = 'Damage fees waived';
        }
        if ($this->rental->lost_fee > 0) {
            $parts[] = 'Lost gear fee ₱' . number_format($this->rental->lost_fee, 2);
        } elseif ($this->rental->lost_fee_waived) {
            $parts[] = 'Lost gear fees waived';
        }

        $message = 'We processed your rental return.';
        if (!empty($parts)) {
            $message .= ' ' . implode(' • ', $parts);
        }
        if ($this->rental->amount_due > 0) {
            $message .= ' Amount due: ₱' . number_format($this->rental->amount_due, 2) . '.';
        } else {
            $message .= ' No additional payment is required.';
        }

        return [
            'type' => 'return_processed',
            'title' => 'Your rental return was processed',
            'message' => $message,
            'rental_id' => $this->rental->id,
            'action_url' => route('rentals.myrentals'),
            'returned_at' => optional($this->rental->returned_at)->toDateTimeString(),
            'late_fee' => $this->rental->late_fee,
            'damage_fee' => $this->rental->damage_fee,
            'lost_fee' => $this->rental->lost_fee,
            'damage_fee_waived' => $this->rental->damage_fee_waived,
            'lost_fee_waived' => $this->rental->lost_fee_waived,
            'waive_reason' => $this->rental->waive_reason,
            'total_charges' => $this->rental->total_charges,
            'amount_due' => $this->rental->amount_due,
        ];
    }
}
