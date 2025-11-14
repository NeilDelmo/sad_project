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
        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Gear item
            $table->integer('quantity')->default(1);
            $table->decimal('price_per_day', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->enum('condition_out', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('condition_in', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('damage_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};
