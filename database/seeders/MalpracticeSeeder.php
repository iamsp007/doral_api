<?php

namespace Database\Seeders;

use App\Models\Malpractice;
use Illuminate\Database\Seeder;

class MalpracticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $types =  [
            [
                'company' => 'MLMIC',
                'email' => 'SYRUWFAX@MLMIC.COM',
                'phone' => '2125769800',
                'status' => 1,
            ],
            [
                'company' => 'PRI',
                'email' => 'UNDERWRITINGCONTACTS@MEDMAL.COM',
                'phone' => '8006326040',
                'status' => 1,
            ],
            [
                'company' => 'MEDPRO',
                'email' => 'MEDPROINDUCTION@MEDPRO.COM',
                'phone' => '8004633776',
                'status' => 1,
            ],
            [
                'company' => 'NSO',
                'email' => 'SERVICE@NSO.COM',
                'phone' => '8002471500',
                'status' => 1,
            ],
            [
                'company' => 'THE DOCTORS COMPANY',
                'email' => 'MEMBERSERVICE@THEDOCTORS.COM',
                'phone' => '8004212368',
                'status' => 1,
            ],
            [
                'company' => 'HPSO',
                'email' => 'SERVICE@HPSO.COM',
                'phone' => '8772152311',
                'status' => 1,
            ],
        ];

        Malpractice::insert($types);
    }
}
