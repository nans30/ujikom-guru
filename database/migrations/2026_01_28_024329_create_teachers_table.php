<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->string('nip', 50)->unique();
            $table->string('name', 150);
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('rfid_uid', 100)->unique()->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });

        // ======================
        // MODULE & PERMISSION
        // (TIDAK DIUTAK-ATIK)
        // ======================
        $actions = [
            'index'  => 'teacher.index',
            'create' => 'teacher.create',
            'edit'   => 'teacher.edit',
            'delete' => 'teacher.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'teachers',
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
        // ❗ FIX PENTING: nama tabel HARUS plural
        Schema::dropIfExists('teachers');
    }
};