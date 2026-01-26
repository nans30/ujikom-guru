<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | RESET CACHE
        |--------------------------------------------------------------------------
        */
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | MODULE + PERMISSION CONFIG
        |--------------------------------------------------------------------------
        */
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
                ]
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
                ]
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

        /*
        |--------------------------------------------------------------------------
        | CREATE MODULES & PERMISSIONS
        |--------------------------------------------------------------------------
        */
        foreach ($modules as $moduleName => $module) {

            Module::updateOrCreate(
                ['name' => $moduleName],
                ['actions' => $module['actions']]
            );

            foreach ($module['actions'] as $actionKey => $permissionName) {

                $permission = Permission::firstOrCreate([
                    'name' => $permissionName
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

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
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
        | USERS
        |--------------------------------------------------------------------------
        */
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Van',
                'last_name'  => 'Ren',
                'password'   => Hash::make('123456789'),
                'system_reserve' => true,
            ]
        );
        $admin->assignRole($adminRole);

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'first_name' => 'Quinn',
                'last_name'  => 'Mcdowell',
                'password'   => Hash::make('123456789'),
                'system_reserve' => false,
            ]
        );
        $user->assignRole($userRole);
    }
}