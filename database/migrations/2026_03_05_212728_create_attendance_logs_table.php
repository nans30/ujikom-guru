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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            // Relasi ke teacher
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Waktu scan RFID
            $table->dateTime('scan_time');

            // Optional kalau nanti ada banyak device
            $table->string('device_id')->nullable();

            $table->timestamps();

            // Biar query cepat
            $table->index(['teacher_id', 'scan_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};