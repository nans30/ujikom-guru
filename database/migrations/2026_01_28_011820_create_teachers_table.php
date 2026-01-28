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
            $table->string('name')->nullable();
            $table->integer('status')->default(1);
            $table->foreignId('created_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        $actions = [
            'index' => 'teacher.index',
            'create' => 'teacher.create',
            'edit' => 'teacher.edit',
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
        Schema::dropIfExists('teacher');
    }
};