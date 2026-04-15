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
        Schema::table('points', function (Blueprint $table) {
            DB::statement("ALTER TABLE points MODIFY COLUMN condition_operator ENUM('<', '>', '=', 'BETWEEN')");
        });
    }

    public function down(): void
    {
        Schema::table('points', function (Blueprint $table) {
             DB::statement("ALTER TABLE points MODIFY COLUMN condition_operator ENUM('<', '>', 'BETWEEN')");
        });
    }
};
