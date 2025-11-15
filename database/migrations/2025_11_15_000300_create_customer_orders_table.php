<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('vendor_id')->constrained('users');
            $table->foreignId('listing_id')->constrained('marketplace_listings');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 12, 2);
            $table->string('status')->default('pending_payment');
            $table->string('proof_photo_path')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
