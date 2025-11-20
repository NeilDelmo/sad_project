<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'trust_score')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('trust_score')->default(100)->after('is_active');
                $table->string('trust_tier')->default('bronze')->after('trust_score');
            });
        }

        if (!Schema::hasTable('trust_transactions')) {
            Schema::create('trust_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('amount'); // positive or negative delta
                $table->string('type'); // order_completed, refund_penalty, manual_adjustment, etc.
                $table->string('reference_type')->nullable(); // polymorphic resource class
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reason')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
                $table->index(['user_id','created_at']);
                $table->index(['reference_type','reference_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('trust_transactions')) {
            Schema::dropIfExists('trust_transactions');
        }
        if (Schema::hasColumn('users', 'trust_score')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['trust_score','trust_tier']);
            });
        }
    }
};
