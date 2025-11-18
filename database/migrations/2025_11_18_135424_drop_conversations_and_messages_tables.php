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
        // Drop messages first due to FK to conversations
        if (Schema::hasTable('messages')) {
            Schema::drop('messages');
        }
        if (Schema::hasTable('conversations')) {
            Schema::drop('conversations');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables for rollback safety (minimal schema)
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();
                $table->index(['buyer_id', 'seller_id']);
                $table->index('last_message_at');
                $table->unique(['buyer_id', 'seller_id', 'product_id']);
            });
        }
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['conversation_id', 'sender_id']);
                $table->index('created_at');
            });
        }
    }
};
