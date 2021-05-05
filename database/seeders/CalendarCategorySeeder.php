<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarCategory;
class CalendarCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CalendarCategory::create([
            'name' => 'Appointment',
            'parent_id' => 0,
            'status'=>1
        ]);

        CalendarCategory::create([
            'name' => 'Remindar',
            'parent_id' => 0,
            'status'=>1
        ]);
        CalendarCategory::create([
            'name' => 'Subcat1',
            'parent_id' => 1,
            'status'=>1
        ]);
        CalendarCategory::create([
            'name' => 'Subcat2',
            'parent_id' => 1,
            'status'=>1
        ]);
        CalendarCategory::create([
            'name' => 'Subcat2',
            'parent_id' => 2,
            'status'=>1
        ]);
    }
}
