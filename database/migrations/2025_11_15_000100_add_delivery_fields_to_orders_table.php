<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('proof_photo_path')->nullable()->after('status');
            $table->text('delivery_notes')->nullable()->after('proof_photo_path');
            $table->timestamp('delivered_at')->nullable()->after('delivery_notes');
            $table->timestamp('received_at')->nullable()->after('delivered_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['proof_photo_path', 'delivery_notes', 'delivered_at', 'received_at']);
        });
    }
};
