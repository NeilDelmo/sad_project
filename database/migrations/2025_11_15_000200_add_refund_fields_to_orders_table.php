<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('refund_reason')->nullable()->after('received_at');
            $table->text('refund_notes')->nullable()->after('refund_reason');
            $table->timestamp('refund_at')->nullable()->after('refund_notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refund_reason', 'refund_notes', 'refund_at']);
        });
    }
};
