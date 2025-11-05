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
       Schema::create('fisherman_profiles', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->integer('experience_years')->nullable();
        $table->string('vessel_name')->nullable();
        $table->string('vessel_type')->nullable();
        $table->string('license_number')->nullable();
        $table->decimal('credit_score', 5, 2)->default(0.00);
        $table->integer('total_transactions')->default(0);
        $table->decimal('compliance_rate', 5, 2)->default(100.00);
        $table->timestamps();
        
        $table->index('user_id');
        $table->index('license_number');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fisherman_profiles');
    }
};
