<?php

namespace Database\Seeders;

use App\Models\Selection;
use Illuminate\Database\Seeder;

class SelectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Selection::truncate();

        $types =  [
            [
                'name' => 'Certifying Board',
                'value' => 'Board 1',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Certifying Board',
                'value' => 'Board 2',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Certifying Board',
                'value' => 'Board 3',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Application Status',
                'value' => 'Active',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Application Status',
                'value' => 'Inactive',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Application Status',
                'value' => 'On Hold',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Relationship',
                'value' => 'Professional',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Relationship',
                'value' => 'Personal',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Address Type',
                'value' => 'Bank Accound Address',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Address Type',
                'value' => 'Office Address',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Legal entity',
                'value' => 'Legal entity 1',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Legal entity',
                'value' => 'Legal entity 2',
                'type' => 'Select',
                'status' => '1',
            ],
            [
                'name' => 'Legal entity',
                'value' => 'Legal entity 3',
                'type' => 'Select',
                'status' => '1',
            ],
        ];

        Selection::insert($types);
    }
}
