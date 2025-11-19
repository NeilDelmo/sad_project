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
        Schema::table('vendor_offers', function (Blueprint $table) {
            $table->decimal('suggested_price_fisherman', 10, 2)->nullable()->after('fisherman_counter_price');
            $table->decimal('ml_confidence_fisherman', 5, 4)->nullable()->after('suggested_price_fisherman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_offers', function (Blueprint $table) {
            $table->dropColumn(['suggested_price_fisherman', 'ml_confidence_fisherman']);
        });
    }
};
