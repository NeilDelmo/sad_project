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
         Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('freshness_metric', 50)->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('available_quantity', 10, 2);
            $table->decimal('seasonality_factor', 5, 2)->nullable();
            
            // Rental fields for gear
            $table->boolean('is_rentable')->default(false);
            $table->decimal('rental_price_per_day', 10, 2)->nullable();
            $table->integer('rental_stock')->default(0);
            $table->integer('rental_available')->default(0);
            $table->enum('rental_condition', ['excellent', 'good', 'fair', 'poor'])->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('supplier_id');
            $table->index('category_id');
            $table->index('name');
            $table->index(['category_id', 'name']);
            $table->index('is_rentable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
