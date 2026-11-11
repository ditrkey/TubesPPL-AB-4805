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
            $table->string('nama');                        // Nama Pelapor
            $table->string('nim', 10);                    // NIM (Wajib 10 digit regex)
            $table->string('jurusan');                     // Program Studi
            $table->string('lokasi');                      // Nama Gedung/Taman/Lapangan Tel-U
            $table->string('location_type')->default('gedung'); // gedung, taman, lapangan, jalan
            $table->decimal('latitude', 10, 7);            // Titik Koordinat Lat
            $table->decimal('longitude', 10, 7);           // Titik Koordinat Lng
            $table->string('photo_path');                  // Path file foto (max 10MB)
            $table->text('pengaduan');                     // Isi pengaduan/deskripsi
            $table->integer('water_depth_cm')->default(30);// Ketinggian air dalam cm
            $table->enum('status', ['pending', 'verified', 'resolved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};