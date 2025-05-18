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
            $table->unsignedBigInteger('id_building')->nullable()->change();
            $table->unsignedBigInteger('id_room')->nullable()->change();
            $table->enum('location_type', ['indoor', 'outdoor'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_report', function (Blueprint $table) {
            $table->unsignedBigInteger('id_building')->nullable(false)->change();
            $table->unsignedBigInteger('id_room')->nullable(false)->change();
            $table->enum('location_type', ['indoor', 'outdoor'])->nullable(false)->change();
        });
    }
};