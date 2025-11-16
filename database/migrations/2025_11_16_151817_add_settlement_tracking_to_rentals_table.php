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
            $table->decimal('deposit_paid', 10, 2)->default(0)->after('deposit_amount');
            $table->decimal('damage_fee', 10, 2)->default(0)->after('deposit_paid');
            $table->decimal('lost_fee', 10, 2)->default(0)->after('damage_fee');
            $table->decimal('total_charges', 10, 2)->default(0)->after('lost_fee');
            $table->decimal('amount_due', 10, 2)->default(0)->after('total_charges');
            $table->string('payment_status')->default('pending')->after('amount_due'); // pending, partial, paid
            $table->timestamp('deposit_paid_at')->nullable()->after('payment_status');
            $table->timestamp('fully_settled_at')->nullable()->after('deposit_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_paid', 'damage_fee', 'lost_fee', 'total_charges', 
                'amount_due', 'payment_status', 'deposit_paid_at', 'fully_settled_at'
            ]);
        });
    }
};
