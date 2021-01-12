<?php

namespace App\Http\Controllers;

use App\Models\PatientMdForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PatientMdFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(),[
           'appointment_id'=>'required|exists:appointments,id',
           'patient_id'=>'required|exists:users,id',
           'physical_examination_report'=>'required|string',
           'authorize_name'=>'required|string',
           'employee_signature'=>'required|string',
           'patient_fname'=>'required|string',
           'patient_lname'=>'required|string',
           'patient_gender'=>'required|string',
           'patient_dob'=>'required|date',
           'patient_doe'=>'required|date',
           'patient_ssn'=>'required',
           'patient_email'=>'required',
           'patient_marital_status'=>'required',
        ]);

        if ($validator->fails()){
            return $this->generateResponse(false,'Invalid Data',$validator->errors(),200);
        }
        try {
            $patientMdForm = new PatientMdForm();
            $patientMdForm->user_id = Auth::user()->id;
            $patientMdForm->appointment_id = $request->appointment_id;
            $patientMdForm->patient_id = $request->patient_id;
            $patientMdForm->physical_examination_report = $request->physical_examination_report;
            $patientMdForm->authorize_name = $request->authorize_name;
            $patientMdForm->employee_signature = $request->employee_signature;
            $patientMdForm->patient_fname = $request->patient_fname;
            $patientMdForm->patient_lname = $request->patient_lname;
            $patientMdForm->patient_gender = $request->patient_gender;
            $patientMdForm->patient_dob = $request->patient_dob;
            $patientMdForm->patient_doe = $request->patient_doe;
            $patientMdForm->patient_ssn = $request->patient_ssn;
            $patientMdForm->patient_email = $request->patient_email;
            $patientMdForm->patient_marital_status = $request->patient_marital_status;

            if ($patientMdForm->save()){
                return $this->generateResponse(true,'Md Form Information Save Successfully!',$patientMdForm,200);
            }
            return $this->generateResponse(false,'Something Went Wrong!',null,422);

        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,422);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientMdForm  $patientMdForm
     * @return \Illuminate\Http\Response
     */
    public function show(PatientMdForm $patientMdForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientMdForm  $patientMdForm
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientMdForm $patientMdForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientMdForm  $patientMdForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientMdForm $patientMdForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientMdForm  $patientMdForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientMdForm $patientMdForm)
    {
        //
    }
}
