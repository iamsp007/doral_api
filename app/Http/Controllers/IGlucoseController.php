<?php

namespace App\Http\Controllers;

//use App\Models\SymptomsMaster;
//use App\Models\Test;
use Illuminate\Http\Request;
use DB;

class IGlucoseController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReading(Request $request)
    {
        $data['reading'] = json_encode($request->all());
        DB::table('iglucose')->insert($data);
    }
}
