<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_thread_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->enum('vote_type', ['upvote', 'downvote']);
            $table->timestamps();
            
            // Ensure one vote per user per thread
            $table->unique(['user_id', 'thread_id']);
            $table->index('thread_id');
            $table->index('user_id');
        });

        Schema::create('forum_reply_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reply_id')->constrained('forum_replies')->onDelete('cascade');
            $table->enum('vote_type', ['upvote', 'downvote']);
            $table->timestamps();
            
            // Ensure one vote per user per reply
            $table->unique(['user_id', 'reply_id']);
            $table->index('reply_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_reply_votes');
        Schema::dropIfExists('forum_thread_votes');
    }
};