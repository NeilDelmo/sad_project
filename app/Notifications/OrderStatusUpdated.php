<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $text)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'order_status',
            'title' => 'Order Update',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => $this->text,
            'link' => '/orders',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Update #' . $this->order->id)
            ->line($this->text)
            ->action('View Order', url('/orders'))
            ->line('Thank you for using our platform!');
    }
}
