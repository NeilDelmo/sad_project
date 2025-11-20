<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CounterVendorOffer extends Notification
{
    use Queueable;

    public function __construct(public VendorOffer $offer)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'counter_vendor_offer',
            'title' => 'Counter Offer Received',
            'message' => sprintf('Fisherman countered at ₱%s for %s', number_format($this->offer->fisherman_counter_price ?? 0, 2), $this->offer->product?->name ?? 'product'),
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

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
