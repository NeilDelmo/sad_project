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
        DB::table('product_categories')
            ->where('name', 'Fresh Fish')
            ->update(['name' => 'Fish']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('product_categories')
            ->where('name', 'Fish')
            ->update(['name' => 'Fresh Fish']);
    }
};
