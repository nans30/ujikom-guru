<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // Contoh: "Kompensasi Telat 30 Menit"
            $table->integer('point_cost'); // Harga item (Poin yang harus dibayar)
            $table->integer('extra_minutes')->default(0);
            $table->integer('stock_limit')->nullable(); // Batas pembelian (Opsional)
            $table->integer('status')->default(1); // 1: Aktif, 0: Nonaktif
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // Modul dan Permissions tetap sama agar sistem akses tidak rusak
        $actions = [
            'index' => 'item.index',
            'create' => 'item.create',
            'edit' => 'item.edit',
            'delete' => 'item.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'items',
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
