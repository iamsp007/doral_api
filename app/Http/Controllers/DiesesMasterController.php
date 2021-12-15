<?php

namespace App\Http\Controllers;

use App\Models\DiesesMaster;

class DiesesMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dieses = DiesesMaster::where('status','=',1)->get();
        if (count($dieses)>0){
            return $this->generateResponse(true,'Dieses Get Successfully!',$dieses,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
