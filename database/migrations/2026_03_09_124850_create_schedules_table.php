<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // relasi ke guru
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();

            // mata pelajaran & kelas
            $table->string('subject');              // Mata pelajaran
            $table->string('class_name')->nullable(); // Kelas

            // hari & jam
            $table->enum('day_of_week', ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
            $table->time('start_time');
            $table->time('end_time');

            $table->integer('status')->default(1);

            // siapa yang buat
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        // modules & permissions tetap sama seperti yang kamu kirim
        $actions = [
            'index' => 'schedule.index',
            'create' => 'schedule.create',
            'edit' => 'schedule.edit',
            'delete' => 'schedule.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'schedules',
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
        Schema::dropIfExists('schedules');
    }
};