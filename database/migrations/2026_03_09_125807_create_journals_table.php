<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            // Relasi ke guru
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // Relasi ke schedule (jadwal pelajaran)
            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete();

            // Deskripsi / catatan tambahan
            $table->text('description')->nullable();

            // Foto bukti kegiatan
            $table->string('photo_url')->nullable();

            // Status jurnal: 0=inactive, 1=active
            $table->integer('status')->default(1);

            // Siapa yang input jurnal
            $table->foreignId('created_by_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        // Module permissions
        $actions = [
            'index' => 'journal.index',
            'create' => 'journal.create',
            'edit' => 'journal.edit',
            'delete' => 'journal.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'journals',
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
        Schema::dropIfExists('journals');
    }
};