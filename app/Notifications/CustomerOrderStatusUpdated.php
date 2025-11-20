<?php

namespace App\Notifications;

use App\Models\CustomerOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomerOrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public CustomerOrder $order, public string $text)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
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
}
