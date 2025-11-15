<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users');
            $table->foreignId('fisherman_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('offer_id')->nullable()->constrained('vendor_offers');
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 12, 2);
            $table->string('status')->default('pending_payment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
