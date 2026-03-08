<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Holiday;

class SyncHolidays extends Command
{
    protected $signature = 'sync:holidays';
    protected $description = 'Sync Indonesian holidays from libur.deno.dev API';

    public function handle()
    {
        $this->info('Ambil data hari libur dari API...');

        try {

            // ambil tahun sekarang
            $year = now()->year;

            // request API
            $response = Http::timeout(10)->get("https://libur.deno.dev/api?year=$year");

            if (!$response->successful()) {
                $this->error('Gagal mengambil data dari API');
                return Command::FAILURE;
            }

            $data = $response->json();

            if (empty($data)) {
                $this->warn('Data holiday kosong dari API');
                return Command::SUCCESS;
            }

            $inserted = 0;

            foreach ($data as $holiday) {

                $date = $holiday['date'] ?? null;
                $name = $holiday['name'] ?? null;

                if (!$date || !$name) {
                    continue;
                }

                Holiday::updateOrCreate(
                    ['date' => $date],
                    [
                        'name' => $name,
                        'description' => $name,
                        'status' => 1,
                        'created_by_id' => 1
                    ]
                );

                $inserted++;
            }

            $this->info("Total holiday berhasil disinkron: {$inserted}");
        } catch (\Exception $e) {

            $this->error('API tidak bisa diakses');
            $this->line($e->getMessage());
        }

        return Command::SUCCESS;
    }
}