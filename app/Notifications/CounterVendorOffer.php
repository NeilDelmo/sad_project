<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CounterVendorOffer extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public VendorOffer $offer)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'counter_vendor_offer',
            'offer_id' => $this->offer->id,
            'product_id' => $this->offer->product_id,
            'product_name' => $this->offer->product?->name,
            'counter_price' => $this->offer->fisherman_counter_price,
            'quantity' => $this->offer->quantity,
            'fisherman_message' => $this->offer->fisherman_message,
            'status' => $this->offer->status,
            'link' => '/vendor/offers?status=countered',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
