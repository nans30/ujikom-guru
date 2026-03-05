<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('date'); // tanggal libur
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        $actions = [
            'index'   => 'holiday.index',
            'create'  => 'holiday.create',
            'edit'    => 'holiday.edit',
            'destroy' => 'holiday.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'holiday',
            'actions' => json_encode($actions),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permissions = [];

        foreach ($actions as $action) {
            $permissions[] = [
                'name' => $action,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('permissions')->insert($permissions);
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');

        DB::table('permissions')->whereIn('name', [
            'holiday.index',
            'holiday.create',
            'holiday.edit',
            'holiday.destroy',
        ])->delete();

        DB::table('modules')->where('name', 'holiday')->delete();
    }
};