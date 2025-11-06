<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_prediction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('wind_speed_kph', 8, 2);
            $table->decimal('wave_height_m', 8, 2);
            $table->decimal('rainfall_mm', 8, 2);
            $table->decimal('tide_level_m', 8, 2);
            $table->unsignedTinyInteger('moon_phase');
            $table->decimal('visibility_km', 8, 2);
            $table->unsignedInteger('past_incidents_nearby');
            $table->string('location', 120);
            $table->string('result');
            $table->text('raw_output')->nullable();
            $table->timestamp('predicted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_prediction_logs');
    }
};
