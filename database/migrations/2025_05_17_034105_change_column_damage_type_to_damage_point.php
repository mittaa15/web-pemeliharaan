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
            $table->renameColumn('damage_type', 'damage_point');
        });

        Schema::table('repair_report', function (Blueprint $table) {
            $table->integer('damage_point')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_report', function (Blueprint $table) {
            $table->string('damage_point')->change();
        });
        Schema::table('repair_report', function (Blueprint $table) {
            $table->renameColumn('damage_point', 'damage_type');
        });
    }
};
