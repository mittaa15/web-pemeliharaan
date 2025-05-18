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
        Schema::table('repair_report', function (Blueprint $table) {
            $table->unsignedBigInteger('id_facility_building')->after('id_room');
            $table->unsignedBigInteger('id_facility_room')->after('id_facility_building');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_report', function (Blueprint $table) {
            $table->dropColumn('id_facility_building');
            $table->dropColumn('id_facility_room');
        });
    }
};
