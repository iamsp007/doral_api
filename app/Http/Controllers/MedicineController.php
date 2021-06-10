<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicineRequest;
use App\Models\Medicine;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($patient_id)
    {
        $data = Medicine::with('dose','from','route','frequency','preferredPharmacy')
            ->where('patient_id','=',$patient_id)
            ->where('status','=','1')
            ->get();
        return $this->generateResponse(true,'Patient Medicine List Get Suucessfully',$data,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MedicineRequest $request)
    {
        $medicine = new Medicine();
        $medicine->patient_id = $request->patient_id;
        $medicine->name = $request->medication;
        $medicine->does = $request->dose;
        $medicine->from = $request->form;
        $medicine->route = $request->route;
        $medicine->amount = $request->amount;
        $medicine->class = $request->class;
        $medicine->frequency = $request->frequency;
        $medicine->start_date = $request->startdate;
        $medicine->order_date = $request->orderdate;
        $medicine->taught_date = $request->taughtdate;
        $medicine->discontinue_date = $request->discontinuedate;
        $medicine->discontinue_order_date = $request->discountinueorderdate;
        $medicine->preferred_pharmacy = $request->preferredPharmacy;
        $medicine->comment = $request->comment;
        if ($request->has('status')){
            $medicine->is_new = $request->status;
        }
        if ($medicine->save()){
            return $this->generateResponse(true,'Add Medicine Successfully',$medicine,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
