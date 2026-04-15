<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nama aturan (misal: "Hadir Tepat Waktu")

            // ======================
            // LOGIC COLUMNS
            // ======================
            $table->enum('condition_operator', ['<', '>', 'BETWEEN'])->nullable();
            $table->string('condition_value')->nullable(); // Nilai pembanding (jam/menit)
            $table->integer('point_modifier')->default(0); // Jumlah poin (+/-)

            $table->integer('status')->default(1);
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // ======================
        // MODULE & PERMISSION
        // ======================
        $actions = [
            'index' => 'point.index',
            'create' => 'point.create',
            'edit' => 'point.edit',
            'delete' => 'point.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'points',
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
        Schema::dropIfExists('points');
    }
};
