<?php

namespace App\Notifications;

use App\Models\CustomerOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CustomerOrder $order, public string $text)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'customer_order_status',
            'title' => 'Order Update',
            'message' => $this->text,
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'link' => '/marketplace/orders?order_id=' . $this->order->id,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Marketplace Order Update #' . $this->order->id)
            ->line($this->text)
            ->action('View Order', url('/marketplace/orders?order_id=' . $this->order->id))
            ->line('Thank you for shopping with us!');
    }
}
