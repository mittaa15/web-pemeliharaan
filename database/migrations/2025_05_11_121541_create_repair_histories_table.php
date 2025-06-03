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
        Schema::create('repair_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_report');
            $table->dateTime('complete_date');
            $table->text('repair_notes')->nullable(); // tambahkan nullable()
            $table->string('damage_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_history');
    }
};