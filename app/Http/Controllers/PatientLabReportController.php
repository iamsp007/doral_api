<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientLabReportRequest;
use App\Models\LabReportType;
use App\Models\PatientLabReport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientLabReportController extends Controller
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
     * @param  \App\Http\Requests\PatientLabReportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PatientLabReportRequest $request)
    {
        $input = $request->all();
        $patientLabReport = new PatientLabReport();
        $patientLabReport->lab_report_type_id = $input['lab_report_type_id'];
        $patientLabReport->user_id = $input['patient_referral_id'];
        if (isset($input['lab_perform_date'])) {
            $patientLabReport->perform_date = date('Y-m-d', strtotime($input['lab_perform_date']));
        }
        if (isset($input['titer'])) {
            $patientLabReport->titer = $input['titer'];
        }

        $patientLabReport->due_date = date('Y-m-d', strtotime($input['lab_due_date']));
        $patientLabReport->expiry_date = date('Y-m-d', strtotime($input['lab_expiry_date']));
        $patientLabReport->result = $input['result'];

        if ($patientLabReport->save()){
            return $this->generateResponse(true, 'Add Patient Lab Report Successfully', $patientLabReport, 200);
        }
        return $this->generateResponse(false, 'Something Went Wrong!', null, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addNote(Request $request)
    {
        $status = 0;
        $data = array();
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);

            $updateRecord = PatientLabReport::where('id', $request['patient_lab_report_id'])->update(['note' => $request['note']]);
            if ($updateRecord) {
                $status = true;
                $message = 'Status updated';
            }

            $data = [
                'patient_lab_report_id' => $updateRecord
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        dd($request->all());
    }
}
