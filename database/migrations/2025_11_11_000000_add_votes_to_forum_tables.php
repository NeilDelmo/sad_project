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
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
        });

        Schema::table('forum_replies', function (Blueprint $table) {
            $table->integer('upvotes')->default(0);
            $table->integer('downvotes')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropColumn(['upvotes', 'downvotes']);
        });

        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropColumn(['upvotes', 'downvotes']);
        });
    }
};
