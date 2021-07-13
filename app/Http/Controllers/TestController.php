<?php

namespace App\Http\Controllers;

use App\Models\SymptomsMaster;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTest(Request $request)
    {
        $input = $request->all();
       
        $categories = Test::where('category_id',$input['category_id'])
            ->where('status',"1")
            ->get();
       
        return $this->generateResponse(true,'Test List',$categories,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMultiTest(Request $request)
    {
        $input = $request->all();
        $data = '';

        if (isset($input['patient_roles_name']) && isset($input['category_id'])) {
            $data = SymptomsMaster::where('dieser_id',$input['category_id'])->where('status',1)->get();
        } else if (isset($input['category_id'])) {
            $data = Test::with('subTestName')->whereIn('category_id',$input['category_id'])
            ->where('status',"1")
            ->get();
        }

        return $this->generateResponse(true,'Test List',$data,200);
    }
}
