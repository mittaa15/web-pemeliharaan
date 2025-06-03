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
        Schema::create('repair_technicians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_report');
            $table->unsignedBigInteger('id_technisian');
            $table->text('description_work');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_technicians');
    }
};
