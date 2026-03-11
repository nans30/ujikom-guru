<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $blueprint) {
            // 1. Hapus Foreign Key lama yang nyambung ke 'users'
            // Nama constraint biasanya: nama_tabel_nama_kolom_foreign
            $blueprint->dropForeign(['evaluatee_id']);

            // 2. Ubah Foreign Key agar nyambung ke tabel 'teachers'
            $blueprint->foreign('evaluatee_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $blueprint) {
            // Balikkan lagi ke 'users' jika migration di-rollback
            $blueprint->dropForeign(['evaluatee_id']);
            $blueprint->foreign('evaluatee_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
