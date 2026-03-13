<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $categories = [
            [
                'name' => 'kompeten',
                'description' => 'Kemampuan mengelola pembelajaran peserta didik.',
                'status' => true,
                'created_by_id' => $adminId,
            ],
            [
                'name' => 'Kepribadian',
                'description' => 'Kemampuan kepribadian yang mantap, berakhlak mulia, arif, dan berwibawa.',
                'status' => true,
                'created_by_id' => $adminId,
            ],
            [
                'name' => 'Sosial',
                'description' => 'Kemampuan guru untuk berkomunikasi dan bergaul secara efektif.',
                'status' => true,
                'created_by_id' => $adminId,
            ],
            [
                'name' => 'Profesional',
                'description' => 'Kemampuan penguasaan materi pelajaran secara luas dan mendalam.',
                'status' => true,
                'created_by_id' => $adminId,
            ],
            [
                'name' => 'Kedisiplinan',
                'description' => 'Ketaatan terhadap tata tertib dan waktu kerja.',
                'status' => true,
                'created_by_id' => $adminId,
            ],
        ];

        foreach ($categories as $category) {
            Categorie::create($category);
        }
    }
}
