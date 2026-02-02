<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Teacher;
use Illuminate\Support\Str;

class SyncTeachers extends Command
{
    protected $signature = 'sync:teachers';
    protected $description = 'Sync teachers from API';

    public function handle()
    {
        $this->info('Ambil data dari API...');

        // =========================
        // HIT API
        // =========================
        $response = Http::timeout(60)->get(
            'https://zieapi.zielabs.id/api/getguru',
            ['tahun' => 2025]
        );

        if (! $response->successful()) {
            $this->error('API gagal diakses');
            return Command::FAILURE;
        }

        $data = $response->json();

        if (!is_array($data)) {
            $this->error('Format data API tidak valid');
            return Command::FAILURE;
        }

        // =========================
        // COUNTER
        // =========================
        $totalApi  = count($data);
        $totalSave = 0;

        $this->info("Total data API: {$totalApi}");
        $bar = $this->output->createProgressBar($totalApi);
        $bar->start();

        // =========================
        // LOOP SYNC
        // =========================
        foreach ($data as $guru) {

            $nip   = trim($guru['nip'] ?? '');
            $nuptk = trim($guru['nuptk'] ?? '');
            $nik   = trim($guru['nik'] ?? '');

            // =========================
            // WAJIB ADA NILAI NIP (NOT NULL)
            // =========================
            $identifier = $nip ?: ($nuptk ?: ($nik ?: 'AUTO-' . Str::random(8)));

            Teacher::updateOrCreate(
                [
                    'nip' => $identifier // UNIQUE + NOT NULL
                ],
                [
                    // isi nip = identifier (AMAN)
                    'nip'   => $identifier,

                    // simpan data asli tetap di kolomnya
                    'nuptk' => $nuptk ?: null,
                    'nik'   => $nik ?: null,

                    'name'          => trim($guru['nama'] ?? '-'),
                    'email'         => $guru['email'] ?? null,
                    'jenis_kelamin' => $guru['jenis_kelamin'] ?? null,
                    'tempat_lahir'  => $guru['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $guru['tanggal_lahir'] ?? null,
                    'photo_url'     => $guru['photo'] ?? null,

                    'is_active'     => true,
                    'created_by_id' => 1,
                ]
            );

            $totalSave++;
            $bar->advance();
        }



        $bar->finish();

        // =========================
        // RESULT
        // =========================
        $this->newLine(2);
        $this->line('---------------------------');
        $this->info("Total API   : {$totalApi}");
        $this->info("Tersimpan   : {$totalSave}");
        $this->line('---------------------------');

        return Command::SUCCESS;
    }
}