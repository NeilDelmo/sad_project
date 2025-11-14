<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendor_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->decimal('purchase_price', 10, 2); // What vendor paid fisherman
            $table->integer('quantity');
            $table->timestamp('purchased_at')->useCurrent();
            $table->enum('status', ['in_stock', 'listed', 'sold'])->default('in_stock');
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_inventory');
    }
};
