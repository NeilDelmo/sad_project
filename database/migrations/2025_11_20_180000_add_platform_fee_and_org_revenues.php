<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add platform_fee column to customer_orders (nullable until we start populating)
        if (Schema::hasTable('customer_orders') && !Schema::hasColumn('customer_orders', 'platform_fee')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->decimal('platform_fee', 10, 2)->nullable()->after('total');
            });
        }

        // Create organization_revenues ledger
        if (!Schema::hasTable('organization_revenues')) {
            Schema::create('organization_revenues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('customer_orders')->cascadeOnDelete();
                $table->foreignId('listing_id')->nullable()->constrained('marketplace_listings')->nullOnDelete();
                $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->decimal('amount', 10, 2); // platform fee actually collected
                $table->string('type', 50)->default('platform_fee');
                $table->timestamp('collected_at');
                $table->timestamps();
                $table->unique(['order_id', 'type']); // prevent duplicate fee rows per order
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('organization_revenues')) {
            Schema::dropIfExists('organization_revenues');
        }
        if (Schema::hasTable('customer_orders') && Schema::hasColumn('customer_orders', 'platform_fee')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->dropColumn('platform_fee');
            });
        }
    }
};
