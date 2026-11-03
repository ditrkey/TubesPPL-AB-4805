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
        Schema::create('parking_zones', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('status_condition'); // aman, siaga, tergenang
        $table->integer('capacity');
        $table->polygon('boundary'); // Spatial type
        $table->timestamps();
        $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
