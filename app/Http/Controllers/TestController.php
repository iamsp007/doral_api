<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

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
}
