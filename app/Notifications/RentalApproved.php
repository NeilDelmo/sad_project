<?php

namespace App\Notifications;

use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RentalApproved extends Notification
{
    use Queueable;

    public $rental;

    /**
     * Create a new notification instance.
     */
    public function __construct(Rental $rental)
    {
        $this->rental = $rental;
    }

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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rental_id' => $this->rental->id,
            'title' => 'Rental Request Approved',
            'message' => 'Your rental request #' . $this->rental->id . ' has been approved! Use the OTP at pickup to activate your rental.',
            'action_url' => route('rentals.myrentals'),
            'action_text' => 'View Rental',
            'rental_date' => $this->rental->rental_date->format('M d, Y'),
            'return_date' => $this->rental->return_date->format('M d, Y'),
            'total_price' => number_format($this->rental->total_price, 2),
            'pickup_otp' => $this->rental->pickup_otp,
            'expires_at' => $this->rental->expires_at ? $this->rental->expires_at->toDateTimeString() : null,
            'type' => 'rental_approved',
        ];
    }
}
