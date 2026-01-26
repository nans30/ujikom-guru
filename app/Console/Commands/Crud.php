<?php

namespace App\Console\Commands;

use App\Http\Services\crud\BuildCrud;
use App\Http\Services\console\CliEcho;
use Illuminate\Console\Command;

class Crud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fungsi untuk membuat CRUD dengan cepat';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $build = (new BuildCrud())->setName($name);
        $build->render();
        $fileList = $build->getFileList();

        $this->info(">>>>>>>>>>>>>>>");
        $build->create();
        foreach ($fileList as $key => $val) {
            $this->info($key);
        }
        $this->info(">>>>>>>>>>>>>>>");
        CliEcho::success('Berhasil membuat CRUD');
        return true;
    }
}
