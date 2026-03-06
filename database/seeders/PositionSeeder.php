<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;

class PositionSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel positions.
     */
    public function run(): void
    {
        // Data posisi dalam bahasa Indonesia
        $positions = [
            ['name' => 'Kepala Sekolah', 'status' => 1],
            ['name' => 'Wakil Kepala Sekolah', 'status' => 1],
            ['name' => 'Guru Matematika', 'status' => 1],
            ['name' => 'Guru Bahasa Indonesia', 'status' => 1],
            ['name' => 'Guru Bahasa Inggris', 'status' => 1],
            ['name' => 'Guru IPA', 'status' => 1],
            ['name' => 'Guru IPS', 'status' => 1],
            ['name' => 'Guru Seni Budaya', 'status' => 1],
            ['name' => 'Guru Olahraga', 'status' => 1],
            ['name' => 'Guru Agama', 'status' => 1],
            ['name' => 'Guru TIK', 'status' => 1],
            ['name' => 'Staf Tata Usaha', 'status' => 1],
            ['name' => 'Staf Perpustakaan', 'status' => 1],
            ['name' => 'Staf Laboratorium', 'status' => 1],
            ['name' => 'Kepala Laboratorium', 'status' => 1],
            ['name' => 'Kepala Perpustakaan', 'status' => 1],
            ['name' => 'Bendahara Sekolah', 'status' => 1],
            ['name' => 'Petugas Kebersihan', 'status' => 1],
            ['name' => 'Security / Satpam', 'status' => 1],
            ['name' => 'Tenaga Administrasi', 'status' => 1],
        ];

        foreach ($positions as $position) {
            Position::create([
                'name' => $position['name'],
                'status' => $position['status'],
                'created_by_id' => Auth::id() ?? 1, // pakai admin 1 kalau Auth kosong
            ]);
        }

        $this->command->info(count($positions) . ' posisi berhasil dibuat!');
    }
}