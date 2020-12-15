<?php

namespace Database\Seeders;

use App\Models\DiesesMaster;
use App\Models\SymptomsMaster;
use Illuminate\Database\Seeder;

class DiesesMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dieses=['pain','fever'.'flue','behavioural'];
        foreach ($dieses as $diese) {
            $dieses = new DiesesMaster();
            $dieses->name = $diese;
            $dieses->img = 'sdfsdfdss';
            $dieses->status = 1;
            if ($dieses->save()){
                $symtomps_arr = ['COUCHIC','SORE THORT','FATICUTE','BODY ACHES'];
                foreach ($symtomps_arr as $item) {
                    $symtomps = new SymptomsMaster();
                    $symtomps->dieser_id = $dieses->id;
                    $symtomps->name = $item;
                    $symtomps->img = 'fghfgdfgf';
                    $symtomps->save();
                }

            }
        }

    }
}
