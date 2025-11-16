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
        Schema::table('rental_items', function (Blueprint $table) {
            $table->string('condition_in_photo')->nullable()->after('condition_in')->comment('Photo of equipment condition upon return');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('maintenance_notes')->nullable()->after('equipment_status')->comment('Maintenance and repair history notes');
            $table->decimal('total_repair_cost', 10, 2)->default(0)->after('maintenance_notes')->comment('Cumulative repair costs');
            $table->timestamp('last_maintenance_date')->nullable()->after('total_repair_cost')->comment('Last maintenance/repair date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_items', function (Blueprint $table) {
            $table->dropColumn('condition_in_photo');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['maintenance_notes', 'total_repair_cost', 'last_maintenance_date']);
        });
    }
};
