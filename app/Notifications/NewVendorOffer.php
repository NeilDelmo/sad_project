<?php

namespace App\Notifications;

use App\Models\VendorOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewVendorOffer extends Notification
{
    use Queueable;

    public function __construct(public VendorOffer $offer)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast(object $notifiable)
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function toDatabase(object $notifiable): array
    {
        $product = $this->offer->product;
        $vendor = $this->offer->vendor;

        return [
            'type' => 'new_vendor_offer',
            'title' => 'New Offer Received',
            'message' => sprintf('%s wants to buy %d %s of %s at ₱%s', $vendor?->username ?? 'A vendor', $this->offer->quantity, $product?->unit_of_measure ?? 'units', $product?->name ?? 'product', number_format($this->offer->offered_price, 2)),
            'offer_id' => $this->offer->id,
            'product_id' => $product?->id,
            'product_name' => $product?->name,
            'vendor_id' => $vendor?->id,
            'vendor_name' => $vendor?->username ?? $vendor?->name,
            'offered_price' => (float) $this->offer->offered_price,
            'quantity' => (int) $this->offer->quantity,
            'vendor_message' => $this->offer->vendor_message,
            'status' => $this->offer->status,
            'link' => '/fisherman/offers?status=pending',
        ];
    }
}
