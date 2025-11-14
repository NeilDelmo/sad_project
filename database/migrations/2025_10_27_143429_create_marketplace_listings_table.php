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
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('asking_price', 10, 2);
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->decimal('demand_factor', 5, 2)->nullable();
            $table->integer('freshness_score')->nullable();
            $table->timestamp('listing_date')->useCurrent();
            $table->timestamp('unlisted_at')->nullable()->index();
            $table->enum('status', ['active', 'sold', 'expired', 'inactive'])->default('active');
            $table->string('freshness_level', 20)->nullable()->index();
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
