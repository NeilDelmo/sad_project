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
        Schema::table('rentals', function (Blueprint $table) {
            $table->timestamp('picked_up_at')->nullable()->after('approved_at');
            $table->timestamp('returned_at')->nullable()->after('picked_up_at');
            $table->decimal('late_fee', 10, 2)->default(0)->after('deposit_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'returned_at', 'late_fee']);
        });
    }
};
