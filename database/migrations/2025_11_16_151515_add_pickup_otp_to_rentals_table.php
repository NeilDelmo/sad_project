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
            $table->string('pickup_otp', 6)->nullable()->after('expires_at');
            $table->timestamp('otp_generated_at')->nullable()->after('pickup_otp');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['pickup_otp', 'otp_generated_at', 'otp_verified_at']);
        });
    }
};
