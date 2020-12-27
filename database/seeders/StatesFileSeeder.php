<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatesFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('sql/states.sql');
        $sql = file_get_contents($path);
        \DB::unprepared($sql);
    }
}
