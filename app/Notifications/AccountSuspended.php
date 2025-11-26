<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSuspended extends Notification
{
    // use Queueable; // Disabled to send immediately

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Account Suspended - SeaLedger')
                    ->greeting('Hello ' . $notifiable->username . ',')
                    ->line('Your account has been suspended by an administrator.')
                    ->line('This action was taken due to a violation of our terms of service or community guidelines.')
                    ->line('If you believe this is a mistake, please contact our support team immediately.')
                    ->action('Contact Support', url('/contact')) // Assuming there is a contact route or just a placeholder
                    ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
