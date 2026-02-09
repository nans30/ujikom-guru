<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // ======================
            // RELATION TO USER (LOGIN)
            // ======================
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ======================
            // DATA UTAMA
            // ======================
            $table->string('nip', 50)->unique();
            $table->string('name', 150);

            // ======================
            // DATA DARI API
            // ======================
            $table->string('nuptk')->nullable();
            $table->string('nik')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            // ======================
            // SYSTEM
            // ======================
            $table->string('photo_url')->nullable();
            $table->string('rfid_uid', 100)->unique()->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });

        // ======================
        // MODULE & PERMISSION
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

        DB::table('permissions')->insert(
            array_map(fn($a) => [
                'name' => $a,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], $actions)
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};