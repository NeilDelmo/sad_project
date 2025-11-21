<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE vendor_inventory MODIFY COLUMN status ENUM('pending_delivery', 'in_stock', 'listed', 'sold', 'refunded') DEFAULT 'pending_delivery'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: If there are any 'refunded' records, this down migration might fail or truncate data.
        DB::statement("ALTER TABLE vendor_inventory MODIFY COLUMN status ENUM('pending_delivery', 'in_stock', 'listed', 'sold') DEFAULT 'pending_delivery'");
    }
};
