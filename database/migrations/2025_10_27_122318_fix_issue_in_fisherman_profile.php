<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fisherman_profiles', function (Blueprint $table) {
            // Drop foreign key using array syntax (recommended)
            $table->dropForeign(['user_id']);
        });

        // For MySQL, we need to use DB::statement to change the column type
        DB::statement('ALTER TABLE fisherman_profiles MODIFY user_id CHAR(36)');

        Schema::table('fisherman_profiles', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fisherman_profiles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Revert back to char(26)
        DB::statement('ALTER TABLE fisherman_profiles MODIFY user_id CHAR(26)');

        Schema::table('fisherman_profiles', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
