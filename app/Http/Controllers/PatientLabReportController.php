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
        $patientLabReport->patient_referral_id = $input['patient_referral_id'];
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

    public function getLabReportReferral(Request $request){
        $roles = Auth::user()->roles->pluck('id')->toArray();

        $labReportTypes = LabReportType::where('status','=','1')
            ->where(function ($q) use ($roles){
                if (count($roles)>1){
                    $q->where('referral_id','=',$roles[1]);
                }

            })
            ->get();
        return $this->generateResponse(true,'lab Report type Referral List',$labReportTypes,200);
    }

    public function labReportUpload(Request $request){
        $roles = Auth::user()->roles->pluck('id')->toArray();
        $this->validate($request, [
            'lab_report_id' => 'required',
            'patient_id' => 'required',
            'files' => 'required'
        ]);
        $data = [];
        if($request->hasfile('files'))
        {
            foreach($request->file('files') as $key=>$file)
            {
                $name = time() .'.'. $file->getClientOriginalName();
                $filePath = 'lab/reports/' . $name;
                Storage::disk('local')->put($filePath,file_get_contents($file));
             //   $file->move(public_path().'/files/', $name);
                $data[$key] = $name;
            }
        }
dd($data);
        $file = $request->filenames;
//            $name = time() . $file->getClientOriginalName();
//            $filePath = 'images/' . $name;
        dd($file);

        if ($request->hasFile('filenames')){
            $file = $request->allFiles();
//            $name = time() . $file->getClientOriginalName();
//            $filePath = 'images/' . $name;
            dd($file);
            Storage::disk('s3')->put($filePath, file_get_contents($file));
        }

        dd($request->all());
    }
}
