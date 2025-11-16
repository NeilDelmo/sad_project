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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('reserved_stock')->default(0)->after('rental_stock')->comment('Units reserved by approved rentals, not yet picked up');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('approved_at')->comment('Approval expiration for pickup');
        });

        Schema::create('rental_item_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rental_item_id');
            $table->string('path');
            $table->timestamps();
            $table->foreign('rental_item_id')->references('id')->on('rental_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('reserved_stock');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });

        Schema::dropIfExists('rental_item_photos');
    }
};
