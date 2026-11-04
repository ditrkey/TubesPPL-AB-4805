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
    Schema::create('field_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users');
        $table->foreignId('report_id')->nullable()->constrained('reports');
        $table->foreignId('sos_alert_id')->nullable()->constrained('sos_alerts');
        $table->foreignId('inventory_id')->constrained('inventories');
        $table->text('instruction');
        $table->enum('status', ['pending', 'in progress', 'completed']);
        $table->timestamps();
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
