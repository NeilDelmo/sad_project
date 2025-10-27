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
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->uuid('listing_id')->primary();
            $table->foreignUuid('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignUuid('seller_id')->constrained('users', 'id')->onDelete('cascade');
            $table->decimal('asking_price', 10, 2);
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->decimal('demand_factor', 5, 2)->nullable();
            $table->integer('freshness_score')->nullable();
            $table->timestamp('listing_date')->useCurrent();
            $table->enum('status', ['active', 'sold', 'expired'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index('product_id');
            $table->index('seller_id');
            $table->index('status');
            $table->index(['status', 'listing_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_listings');
    }
};
