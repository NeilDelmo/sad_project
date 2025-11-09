<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_prediction_logs', function (Blueprint $table) {
            $table->decimal('latitude', 10, 6)->nullable()->after('location');
            $table->decimal('longitude', 10, 6)->nullable()->after('latitude');
            $table->tinyInteger('risk_level')->nullable()->after('result');
            $table->decimal('confidence', 5, 4)->nullable()->after('risk_level');
            $table->json('override_reasons')->nullable()->after('confidence');
            $table->json('environmental_flags')->nullable()->after('override_reasons');
            $table->string('data_source', 32)->default('manual')->after('environmental_flags');
            $table->index(['latitude', 'longitude'], 'risk_logs_lat_lon_index');
        });
    }

    public function down(): void
    {
        Schema::table('risk_prediction_logs', function (Blueprint $table) {
            $table->dropIndex('risk_logs_lat_lon_index');
            $table->dropColumn([
                'latitude',
                'longitude',
                'risk_level',
                'confidence',
                'override_reasons',
                'environmental_flags',
                'data_source',
            ]);
        });
    }
};
