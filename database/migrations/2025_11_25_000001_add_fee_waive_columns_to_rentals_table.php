<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->boolean('damage_fee_waived')->default(false)->after('damage_fee');
            $table->boolean('lost_fee_waived')->default(false)->after('damage_fee_waived');
            $table->text('waive_reason')->nullable()->after('lost_fee_waived');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['damage_fee_waived', 'lost_fee_waived', 'waive_reason']);
        });
    }
};
