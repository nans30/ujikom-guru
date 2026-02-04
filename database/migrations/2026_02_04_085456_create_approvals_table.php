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

            // tanggal izin
            $table->date('date');

            // izin / sakit
            $table->enum('type', ['izin', 'sakit']);

            // alasan
            $table->text('reason')->nullable();

            // bukti file
            $table->string('proof_file')->nullable();

            // pending | approved | rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            // siapa yang approve (admin/operator)
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });


        /*
        |--------------------------------------------------------------------------
        | Permission (optional, biar sama sistem kamu)
        |--------------------------------------------------------------------------
        */

        $actions = [
            'index'  => 'approval.index',
            'approve' => 'approval.approve',
            'reject' => 'approval.reject',
        ];

        DB::table('modules')->insert([
            'name' => 'approvals',
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
        Schema::dropIfExists('approvals');
    }
};