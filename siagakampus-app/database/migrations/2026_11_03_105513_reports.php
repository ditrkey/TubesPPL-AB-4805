<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();

            // Kolom dari main (PBI Pelaporan Warga) - dibuat nullable agar input manual Admin PBI-05 tidak error
            $table->string('nama')->nullable();
            $table->string('nim', 10)->nullable();
            $table->string('jurusan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('location_type')->default('gedung');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('pengaduan')->nullable();
            $table->integer('water_depth_cm')->default(30);

            // Kolom tambahan untuk PBI-05 (Manajemen & Verifikasi Laporan Admin)
            $table->string('title')->nullable();
            $table->string('category')->default('Banjir');
            $table->enum('water_level', ['Low', 'Med', 'High'])->default('Low');
            $table->text('description')->nullable();
            $table->string('photo_path');
            $table->geometry('location', subtype: 'point', srid: 4326)->nullable();
            $table->enum('status', ['Pending', 'Verified', 'Handling', 'Resolved', 'Rejected'])->default('Pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};