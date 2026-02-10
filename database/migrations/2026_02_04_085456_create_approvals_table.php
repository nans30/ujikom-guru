<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();

            // guru yang mengajukan
            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            // jenis pengajuan
            $table->enum('type', ['izin', 'sakit', 'cuti']);

            // range tanggal (multi hari)
            $table->date('start_date');
            $table->date('end_date');

            // alasan
            $table->text('reason')->nullable();

            // bukti file
            $table->string('proof_file')->nullable();

            // status approval
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // admin/operator yang approve
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * === PERMISSION ===
         */
        $actions = [
            'index'   => 'approval.index',
            'create'  => 'approval.create',
            'approve' => 'approval.approve',
            'reject'  => 'approval.reject',
        ];

        DB::table('modules')->insert([
            'name' => 'approvals',
            'actions' => json_encode($actions),
        ]);

        $permissions = array_map(fn($action) => [
            'name' => $action,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ], $actions);

        DB::table('permissions')->insert($permissions);
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};