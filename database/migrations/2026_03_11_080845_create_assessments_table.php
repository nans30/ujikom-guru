<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();

            // Siapa yang menilai (Admin/Kepala Sekolah)
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();

            // Siapa yang dinilai (Guru/Staf) 
            // Catatan: Jika nanti kamu jalankan migration "change evaluatee_id to teachers", 
            // pastikan tabel teachers sudah ada.
            $table->foreignId('evaluatee_id')->constrained('users')->cascadeOnDelete();

            $table->date('assessment_date');

            // --- MODIFIKASI DISINI ---
            $table->enum('semester', ['1', '2']); // 1 = Ganjil, 2 = Genap
            $table->string('academic_year', 10);   // Contoh: "2025/2026"
            // -------------------------

            $table->text('general_notes')->nullable();
            $table->integer('status')->default(1); // 1 = Draft, 2 = Final

            $table->timestamps();
            $table->softDeletes();
        });

        $actions = [
            'index'  => 'assessment.index',
            'create' => 'assessment.create',
            'show'   => 'assessment.show',
            'edit'   => 'assessment.edit',
            'delete' => 'assessment.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'assessments',
            'actions' => json_encode($actions),
        ]);

        $permissions = array_map(function ($action) {
            return [
                'name' => $action,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $actions);

        DB::table('permissions')->insert($permissions);
    }

    public function down(): void
    {
        // Perbaikan typo dari 'assessment' ke 'assessments'
        Schema::dropIfExists('assessments');
    }
};
