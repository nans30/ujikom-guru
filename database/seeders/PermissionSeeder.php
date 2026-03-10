<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================
         * Attendance Report
         * =========================
         */
        Permission::firstOrCreate([
            'name' => 'attendance.report',
            'guard_name' => 'web',
        ]);

        /**
         * =========================
         * Teacher Report
         * =========================
         */
        Permission::firstOrCreate([
            'name' => 'teacher.report',
            'guard_name' => 'web',
        ]);

        /**
         * =========================
         * Position Report
         * =========================
         */
        Permission::firstOrCreate([
            'name' => 'position.report',
            'guard_name' => 'web',
        ]);

        /**
         * =========================
         * Holiday Report
         * =========================
         */
        Permission::firstOrCreate([
            'name' => 'holiday.report',
            'guard_name' => 'web',
        ]);

        /**
         * =========================
         * Journal Report
         * =========================
         */
        Permission::firstOrCreate([
            'name' => 'journal.report',
            'guard_name' => 'web',
        ]);

        /**
         * =========================
         * Assign ke Admin
         * =========================
         */
        $admin = Role::where('name', 'admin')->first();

        if ($admin) {

            $admin->givePermissionTo([
                'attendance.report',
                'teacher.report',
                'position.report',
                'holiday.report',
                'journal.report',
            ]);
        }
    }
}
