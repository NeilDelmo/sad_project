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
            $table->unsignedInteger('maintenance_count')->default(0)->after('rental_stock')->comment('Units currently in maintenance');
        });

        Schema::table('rental_items', function (Blueprint $table) {
            $table->unsignedInteger('good_count')->nullable()->after('quantity');
            $table->unsignedInteger('fair_count')->nullable()->after('good_count');
            $table->unsignedInteger('damaged_count')->nullable()->after('fair_count');
            $table->unsignedInteger('lost_count')->nullable()->after('damaged_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('maintenance_count');
        });

        Schema::table('rental_items', function (Blueprint $table) {
            $table->dropColumn(['good_count', 'fair_count', 'damaged_count', 'lost_count']);
        });
    }
};
