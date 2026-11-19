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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('title');
            $table->string('category')->default('Banjir'); // 'Banjir', 'Drainase', 'Akses Tertutup', 'Lainnya'
            $table->enum('water_level', ['Low', 'Med', 'High'])->default('Low'); // Low (<15 cm), Med (15-40 cm), High (>40 cm)
            $table->text('description');
            $table->string('photo_path');
            $table->geometry('location', subtype: 'point', srid: 4326); // MySQL Spatial POINT (SRID 4326)
            $table->spatialIndex('location');
            $table->enum('status', ['Pending', 'Verified', 'Handling', 'Resolved', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
