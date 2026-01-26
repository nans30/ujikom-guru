<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'users' => [
                'actions' => [
                    'index' => 'user.index',
                    'create'  => 'user.create',
                    'edit'    => 'user.edit',
                    'destroy' => 'user.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'destroy'],
                ]
            ],
            'roles' => [
                'actions' => [
                    'index'   => 'role.index',
                    'create'  => 'role.create',
                    'edit'    => 'role.edit',
                    'destroy'  => 'role.destroy'
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
                    'destroy'   => 'page.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'destroy'],
                ]
            ],
            'settings' => [
                'actions' => [
                    'index'   => 'setting.index',
                    'edit'    => 'setting.edit',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'edit'],
                ],
            ],
        ];

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $userpermision = [];

        foreach ($modules as $key => $value) {
            Module::updateOrCreate(['name' => $key], ['name' => $key, 'actions' => $value['actions']]);
            foreach ($value['actions'] as $key => $permission) {
                if (!Permission::where('name', $permission)->first()) {
                    $permission = Permission::create(['name' => $permission]);
                }
                if (isset($value['roles'])) {
                    foreach ($value['roles'] as $role => $allowed_actions) {
                        if ($role == RoleEnum::USER) {
                            if (in_array($key, $allowed_actions)) {
                                $userpermision[] = $permission;
                            }
                        }
                    }
                }
            }
        }

        $admin = Role::create([
            'name' => RoleEnum::ADMIN,
            'system_reserve' => true
        ]);

        $admin->givePermissionTo(Permission::all());
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('123456789'),
            'gender' => 'male',
            'dob' => '01/01/2000',
            'location' => 'Rome',
           
            'phone' => '612345678',
            'skills' => 'Developer',
            'about_me' => 'Administrator',
            'first_name' => 'Van',
            'last_name' => 'Ren',
            'postal_code' => '00100',
            'address' => 'Rome',
          
          
            'bio' => 'Developer',
            'system_reserve' => true,
        ]);
        $user->assignRole($admin);
        $image = public_path('admin/assets/images/user-images/sui.gif');
        if (File::exists($image)) {
            $user->addMedia($image)->toMediaCollection('image');
        }

        $userRole = Role::create([
            'name' => RoleEnum::USER,
            'system_reserve' => false
        ]);

        $userRole->givePermissionTo($userpermision);
        $user = User::factory()->create([
            'first_name' => 'Quinn',
            'last_name' => 'Mcdowell',
            'email' => 'user@example.com',
            'password' => Hash::make('123456789'),
            'country_code' => '33',
            'phone' => '0123456789',
            'dob' => '08/11/2024',
            'gender' => 'male',
            'status' => '1',
           
       
            'location' => 'Paris',
            'postal_code' => '75001',
            'about_me' => 'I like using platforms that make life easier. As a user, I value efficiency and creativity, and I am always on the lookout for exciting features and content. I appreciate how easy it is to navigate this space and find what I am looking for.',
            'bio' => 'Quinn Mcdowell is a regular user who enjoys exploring the platform and utilizing its features. He is a curious individual who values simplicity and ease of access in everything he does. Quinn frequently seeks out new content and loves discovering innovative ideas.',
            'system_reserve' => false,
        ]);
        $user->assignRole($userRole);
        $image = public_path('admin/assets/images/user-images/user.jpg');
        if (File::exists($image)) {
            $user->addMedia($image)->toMediaCollection('image');
        }

        $names = [
            'Shirakami Fubuki',
            'Hoshimachi Suisei',
            'Peko Mama',
            'Usada Pekora',
            'Nekomata Okayu',
            'Natsuiro Matsuri',
            'Ozora Subaru',
            'Housho Marine',
            'Laplace Darknes',
            'Kanata Amane',
        ];

        $aboutMes = [
            'I love discovering new features on this platform.',
            'User experience is important to me, and I enjoy seamless navigation.',
            'I often explore the platform in my free time.',
            'I value creativity and useful content.',
            'I’m here to learn and share with others.',
            'Using this platform is part of my daily routine.',
            'I enjoy interacting with interesting tools and people.',
            'Always curious about new trends and features.',
            'I like keeping things simple and effective.',
            'This platform helps me be more productive every day.',
        ];

        $bios = [
            'A cheerful person who enjoys browsing and learning.',
            'Tech-savvy individual who loves exploring new tools.',
            'A curious mind always looking for inspiration.',
            'Loves connecting with people and discovering content.',
            'Enjoys simplicity and good design in digital products.',
            'Spends time learning and trying new features.',
            'Finds joy in interactive and intuitive platforms.',
            'Passionate about digital experiences and growth.',
            'Friendly user who appreciates innovation.',
            'Always looking to improve and discover new things.',
        ];

        foreach ($names as $index => $fullName) {
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            $user = User::factory()->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => strtolower(str_replace(' ', '.', $fullName)) . '@example.com',
                'password' => Hash::make('123456789'),
                'country_code' => '33',
                'phone' => '012345678' . $index,
                'dob' => '08/11/2024',
                'gender' => 'female',
                'status' => '1',
              
                'location' => 'Tokyo',
                'postal_code' => '100-0001',
                'about_me' => Arr::random($aboutMes),
                'bio' => Arr::random($bios),
                'system_reserve' => false,
            ]);

            // Assign role
            $user->assignRole($userRole);

            // Attach photo if exists
            $imagePath = public_path("admin/assets/images/user-images/" . ($index + 1) . ".jpeg");
            if (File::exists($imagePath)) {
                $user->addMedia($imagePath)->toMediaCollection('image');
            }
        }
    }
}