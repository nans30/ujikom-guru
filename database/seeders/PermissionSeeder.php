<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================
        // Buat permission attendance report
        // =========================
        Permission::firstOrCreate([
            'name' => 'attendance.report',
            'guard_name' => 'web',
        ]);

        // =========================
        // Buat permission teacher report
        // =========================
        Permission::firstOrCreate([
            'name' => 'teacher.report',
            'guard_name' => 'web',
        ]);

        // =========================
        // Ambil role admin
        // =========================
        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            // Beri permission ke admin
            $admin->givePermissionTo('attendance.report');
            $admin->givePermissionTo('teacher.report');
        }
    }
}