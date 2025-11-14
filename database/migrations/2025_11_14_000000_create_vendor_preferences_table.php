<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('preferred_categories')->nullable();
            $table->unsignedInteger('min_quantity')->nullable();
            $table->decimal('max_unit_price', 10, 2)->nullable();
            $table->json('notify_channels')->nullable();
            $table->string('notify_on')->default('matching');
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_preferences');
    }
};
