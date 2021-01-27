<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCityFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import City';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();
        $path = public_path('sql/cities.sql');
        $sql = file_get_contents($path);
        sleep(30);
        $progressBar->advance();
        \DB::unprepared($sql);
        $progressBar->finish();
    }
}
