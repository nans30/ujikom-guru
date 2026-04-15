<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Point;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name'               => 'hadir',
                'condition_operator' => 'BETWEEN',
                'condition_value'    => '19:00-19:47',
                'point_modifier'     => 10,
                'status'             => 1,
                'created_by_id'      => 1,
            ],
            [
                'name'               => 'telat',
                'condition_operator' => '>',
                'condition_value'    => '19:48',
                'point_modifier'     => -5,
                'status'             => 1,
                'created_by_id'      => 1,
            ],
            [
                'name'               => 'alpha',
                'condition_operator' => '=',
                'condition_value'    => 'alpha',
                'point_modifier'     => -10,
                'status'             => 1,
                'created_by_id'      => 1,
            ],
        ];

        foreach ($rules as $rule) {
            Point::updateOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}
