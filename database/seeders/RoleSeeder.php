<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\File;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // RESET PERMISSION CACHE
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // MODULE & PERMISSION CONFIG
        $modules = [
            'users' => [
                'actions' => [
                    'index'   => 'user.index',
                    'create'  => 'user.create',
                    'edit'    => 'user.edit',
                    'destroy' => 'user.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::USER  => ['index'],
                ],
            ],
            'roles' => [
                'actions' => [
                    'index'   => 'role.index',
                    'create'  => 'role.create',
                    'edit'    => 'role.edit',
                    'destroy' => 'role.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'destroy'],
                ],
            ],
            'pages' => [
                'actions' => [
                    'index'   => 'page.index',
                    'create'  => 'page.create',
                    'edit'    => 'page.edit',
                    'destroy' => 'page.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::USER  => ['index'],
                ],
            ],
            'settings' => [
                'actions' => [
                    'index' => 'setting.index',
                    'edit'  => 'setting.edit',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'edit'],
                ],
            ],
        ];

        $adminPermissions = [];
        $userPermissions  = [];

        // CREATE MODULES & PERMISSIONS
        foreach ($modules as $moduleName => $module) {

            Module::updateOrCreate(
                ['name' => $moduleName],
                ['actions' => $module['actions']]
            );

            foreach ($module['actions'] as $actionKey => $permissionName) {

                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                ]);

                // ADMIN
                if (
                    isset($module['roles'][RoleEnum::ADMIN]) &&
                    in_array($actionKey, $module['roles'][RoleEnum::ADMIN])
                ) {
                    $adminPermissions[] = $permission;
                }

                // USER
                if (
                    isset($module['roles'][RoleEnum::USER]) &&
                    in_array($actionKey, $module['roles'][RoleEnum::USER])
                ) {
                    $userPermissions[] = $permission;
                }
            }
        }

        // CREATE ROLES
        $adminRole = Role::firstOrCreate(
            ['name' => RoleEnum::ADMIN],
            ['system_reserve' => true]
        );
        $adminRole->syncPermissions($adminPermissions);

        $userRole = Role::firstOrCreate(
            ['name' => RoleEnum::USER],
            ['system_reserve' => false]
        );
        $userRole->syncPermissions($userPermissions);

        /*
        |--------------------------------------------------------------------------
        | CREATE ADMIN USERS
        |--------------------------------------------------------------------------
        */

        $admins = [
            [
                'name'  => 'Van Ren',
                'email' => 'admin@example.com',
            ],
            [
                'name'  => 'kepala sekolah',
                'email' => 'kepala@example.com',
            ],
        ];

        $imagePath = public_path('admin/assets/images/user-images/pp.png');

        foreach ($admins as $adminData) {

            $admin = User::firstOrCreate(
                ['email' => $adminData['email']],
                [
                    'name'           => $adminData['name'],
                    'password'       => Hash::make('123456789'),
                    'system_reserve' => true,
                ]
            );

            $admin->assignRole($adminRole);

            // ADD IMAGE
            if (File::exists($imagePath)) {
                if (!$admin->getFirstMedia('image')) {
                    $admin->addMedia($imagePath)->toMediaCollection('image');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE NORMAL USER
        |--------------------------------------------------------------------------
        */

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'           => 'Quinn Mcdowell',
                'password'       => Hash::make('123456789'),
                'system_reserve' => false,
            ]
        );

        $user->assignRole($userRole);
    }
}
