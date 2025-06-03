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
        Schema::table('repair_schedule', function (Blueprint $table) {
            $table->string('technician_name')->nullable()->after('id_report');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_schedule', function (Blueprint $table) {
            $table->dropColumn('technician_name');
        });
    }
};
