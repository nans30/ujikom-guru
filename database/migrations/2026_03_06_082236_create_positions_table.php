<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * ============================
         * TABEL POSITIONS / JABATAN
         * ============================
         */
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('status')->default(1);
            $table->foreignId('created_by_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ============================
        // MODULE & PERMISSION UNTUK POSITIONS
        // ============================
        $actions = [
            'index'  => 'position.index',
            'create' => 'position.create',
            'edit'   => 'position.edit',
            'delete' => 'position.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'positions',
            'actions' => json_encode($actions),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissions = array_map(fn($action) => [
            'name' => $action,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ], $actions);

        DB::table('permissions')->insert($permissions);

        /**
         * ============================
         * UPDATE TEACHERS TABLE
         * ============================
         */
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete()
                ->after('name'); // kolom letaknya setelah name
        });
    }

    public function down(): void
    {
        // DROP kolom position_id di teachers
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
        });

        // DROP tabel positions
        Schema::dropIfExists('positions');

        // Hapus permission & module
        DB::table('permissions')->whereIn('name', [
            'position.index',
            'position.create',
            'position.edit',
            'position.destroy',
        ])->delete();

        DB::table('modules')->where('name', 'positions')->delete();
    }
};