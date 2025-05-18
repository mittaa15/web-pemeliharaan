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
        Schema::create('repair_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_building');
            $table->unsignedBigInteger('id_room');
            $table->text('damage_description');
            $table->string('damage_photo');
            $table->string('status');
            $table->dateTime('submission_date');
            $table->enum('location_type', ['indoor', 'outdoor']);
            $table->string('facility'); 
            $table->string('damage_impact'); 
            $table->string('damage_type'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_report');
    }
};