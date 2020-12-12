<?php

namespace App\Http\Controllers;

use App\Models\DiesesMaster;
use Illuminate\Http\Request;

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DiesesMaster  $diesesMaster
     * @return \Illuminate\Http\Response
     */
    public function show(DiesesMaster $diesesMaster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DiesesMaster  $diesesMaster
     * @return \Illuminate\Http\Response
     */
    public function edit(DiesesMaster $diesesMaster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DiesesMaster  $diesesMaster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DiesesMaster $diesesMaster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DiesesMaster  $diesesMaster
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiesesMaster $diesesMaster)
    {
        //
    }
}
