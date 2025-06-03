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
        Schema::table('complaint', function (Blueprint $table) {
            $table->dropColumn('complaint_status');
            $table->dropColumn('complaint_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaint', function (Blueprint $table) {
            $table->string('complaint_status')->nullable();
            $table->date('complaint_date')->nullable();
        });
    }
};