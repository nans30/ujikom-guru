<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_tokens', function (Blueprint $table) {
            $table->id();
            // Diubah ke 'items' karena tadi kita ganti nama tabel fleksibelnya
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();

            $table->enum('status', ['AVAILABLE', 'USED', 'EXPIRED'])->default('AVAILABLE');

            // Relasi ke attendance saat token ini dipakai otomatis
            $table->foreignId('used_at_attendance_id')
                ->nullable()
                ->constrained('attendances')
                ->nullOnDelete();

            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_tokens');
    }
};
