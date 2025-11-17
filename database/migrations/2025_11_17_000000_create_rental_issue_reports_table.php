<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('issue_type')->default('other'); // pre_existing, accidental, lost, other
            $table->string('severity')->nullable(); // low, medium, high
            $table->string('title')->nullable();
            $table->text('description');
            $table->json('photos')->nullable();
            $table->string('status')->default('open'); // open, under_review, resolved
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_issue_reports');
    }
};
