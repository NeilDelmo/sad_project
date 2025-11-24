<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pricing_prediction_logs', function (Blueprint $table) {
            $table->id();
            $table->string('context');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained('vendor_offers')->nullOnDelete();
            $table->foreignId('listing_id')->nullable()->constrained('marketplace_listings')->nullOnDelete();
            $table->decimal('multiplier', 6, 3)->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->boolean('used_fallback')->default(false);
            $table->integer('runtime_ms')->nullable();
            $table->json('features')->nullable();
            $table->json('signals')->nullable();
            $table->json('extra')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['context', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_prediction_logs');
    }
};
