<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
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
        // Buat permission position report
        // =========================
        Permission::firstOrCreate([
            'name' => 'position.report',
            'guard_name' => 'web',
        ]);

        // =========================
        // Ambil role admin
        // =========================
        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            $admin->givePermissionTo('attendance.report');
            $admin->givePermissionTo('teacher.report');
            $admin->givePermissionTo('position.report');
        }
    }
}