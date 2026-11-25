<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Titik Lokasi Inventory (1 Lokasi = 1 Ikon BACKPACK di view.map)
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Admin/Dosen penginput
            $table->string('lokasi');                                 // Nama Gedung/Taman/Lapangan Tel-U
            $table->enum('location_type', ['gedung', 'taman', 'lapangan'])->default('gedung');
            $table->decimal('latitude', 10, 7);                       // Sama seperti PBI 4
            $table->decimal('longitude', 10, 7);                      // Sama seperti PBI 4
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Detail Stok Atribut di Lokasi Tersebut
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory_locations')->cascadeOnDelete();
            $table->string('atribut');                                // Salah satu dari 11 atribut
            $table->integer('jumlah')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['inventory_id', 'atribut']);              // Mencegah duplikat atribut di 1 lokasi
        });

        // 3. Log Form Pemakaian / Pengambilan Atribut
        Schema::create('inventory_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventory_locations')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama');                                   // Nama Pengambil
            $table->string('nim', 10);                                // NIM (10 digit, sama seperti PBI 4)
            $table->integer('jumlah_diambil')->default(1);
            $table->text('alasan');                                   // Alasan pemakaian
            $table->string('photo_path');                             // Foto bukti atribut
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_usages');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_locations');
    }
};
