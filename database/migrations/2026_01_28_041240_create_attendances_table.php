<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // relasi guru
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // tanggal absensi
            $table->date('date');

            // waktu check-in & check-out
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();

            // metode absensi
            $table->enum('method_in', ['rfid', 'manual'])->nullable();
            $table->enum('method_out', ['rfid', 'manual'])->nullable();

            // bukti
            $table->string('photo')->nullable();
            $table->string('proof_file')->nullable();

            // status kehadiran
            $table->enum('status', [
                'hadir',
                'telat',
                'izin',
                'sakit',
                'alpha'
            ])->default('alpha');

            // alasan izin / sakit
            $table->string('reason')->nullable();

            // durasi keterlambatan (menit)
            $table->integer('late_duration')->nullable();

            // siapa yang input (manual)
            $table->foreignId('created_by_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // cegah double absensi guru di hari yang sama
            $table->unique(['teacher_id', 'date']);
        });

        /**
         * === JANGAN DIUBAH ===
         * Permission & module tetap seperti punyamu
         */
        $actions = [
            'index'  => 'attendance.index',
            'create' => 'attendance.create',
            'edit'   => 'attendance.edit',
            'delete' => 'attendance.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'attendances',
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
        Schema::dropIfExists('attendances');
    }
};