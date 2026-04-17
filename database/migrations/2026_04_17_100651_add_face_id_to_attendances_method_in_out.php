<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter enum to include 'face_id'
        DB::statement("ALTER TABLE attendances MODIFY COLUMN method_in ENUM('rfid', 'manual', 'face_id') NULL");
        DB::statement("ALTER TABLE attendances MODIFY COLUMN method_out ENUM('rfid', 'manual', 'face_id') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE attendances MODIFY COLUMN method_in ENUM('rfid', 'manual') NULL");
        DB::statement("ALTER TABLE attendances MODIFY COLUMN method_out ENUM('rfid', 'manual') NULL");
    }
};
