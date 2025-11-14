<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Notifications\Notification;

class NewCatchAvailable extends Notification
{
    public function __construct(public Product $product)
    {
    }

    public function via($notifiable): array
    {
        // Use database by default; email can be added later
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'name' => $this->product->name,
            'category_id' => $this->product->category_id,
            'unit_price' => $this->product->unit_price,
            'available_quantity' => $this->product->available_quantity,
            'supplier_id' => $this->product->supplier_id,
            'created_at' => $this->product->created_at,
        ];
    }
}
