```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {

            $table->id();

            // nama kategori / indikator
            $table->string('name');

            // deskripsi indikator
            $table->text('description')->nullable();

            // status aktif / nonaktif
            $table->boolean('status')->default(true);

            // siapa yang membuat kategori
            $table->foreignId('created_by_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        $actions = [
            'index' => 'categorie.index',
            'create' => 'categorie.create',
            'edit' => 'categorie.edit',
            'delete' => 'categorie.destroy',
        ];

        DB::table('modules')->insert([
            'name' => 'categories',
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
        Schema::dropIfExists('categories');
    }
};