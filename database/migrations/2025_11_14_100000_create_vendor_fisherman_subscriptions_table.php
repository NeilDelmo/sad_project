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
        Schema::create('vendor_fisherman_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('fisherman_id')->constrained('users')->onDelete('cascade');
            $table->enum('notification_preference', ['sms', 'email', 'app', 'all'])->default('app');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure a vendor can't subscribe to the same fisherman twice
            $table->unique(['vendor_id', 'fisherman_id']);
            
            // Indexes for queries
            $table->index('vendor_id');
            $table->index('fisherman_id');
            $table->index(['vendor_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_fisherman_subscriptions');
    }
};
