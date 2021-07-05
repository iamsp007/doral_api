<?php

namespace App\Http\Controllers;

use App\Models\SymptomsMaster;

class SymptomsMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($dieser_id)
    {
        $data = SymptomsMaster::where([['dieser_id','=',$dieser_id],['status','=',1]])->get();
        if (count($data)>0){
            return $this->generateResponse(true,'Symptoms Get Successfully!',$data,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
