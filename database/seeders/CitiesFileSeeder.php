<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        sleep(1);
//        $this->command->getOutput()->progressAdvance();
        $this->command->info('City table seeding Start!');
        $path = public_path('sql/cities.sql');
        DB::unprepared(file_get_contents($path));
        $this->command->info('City table seeded!');

    }
}
