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
        DB::statement("ALTER TABLE vendor_offers MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'countered', 'expired', 'auto_rejected', 'withdrawn', 'closed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: If there are any 'closed' records, this down migration might fail or truncate data depending on strict mode.
        // Ideally we would handle that, but for now we just revert the definition.
        DB::statement("ALTER TABLE vendor_offers MODIFY COLUMN status ENUM('pending', 'accepted', 'rejected', 'countered', 'expired', 'auto_rejected', 'withdrawn') DEFAULT 'pending'");
    }
};
