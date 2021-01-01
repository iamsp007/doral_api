<?php

namespace App\Http\Controllers;

use App\Models\PatientReferral;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Excel;
use App\Imports\BulkImport;
use App\Imports\BulkCertImport;

class PatientReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $data = array();
        try {
            $patientReferral = patientReferral::with('detail', 'service', 'filetype', 'mdforms', 'plans')
            ->where('service_id', $id)
            ->whereNotNull('first_name')
            ->get();
            if (!$patientReferral) {
                throw new Exception("No Referance Patients are registered");
            }
            $data = [
                'patientReferral' => $patientReferral
            ];
            //return $this->generateResponse(true, 'Referance Patients!', $data);
            return $this->generateResponse(true,'Referance Patients!',$patientReferral,200);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
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
        $this->validate($request,[
            'file_name'=>'required',
            'file_type'=>'required',
            'referral_id'=>'required',
            'service_id'=>'required',
        ]);
        try {

            $folder = 'csv';
            if ($request->file_type===1){
                $folder = "demographic";
            }elseif ($request->file_type===2){
                $folder = "clinical";
            }elseif ($request->file_type===3){
                $folder = "compliance_due";
            }elseif ($request->file_type===4){
                $folder = "previous_md";
            }

            // upload file
            if ($request->hasFile('file_name')) {
                $filenameWithExt = $request->file('file_name')->getClientOriginalName();
                $filename =  preg_replace("/[^a-z0-9\_\-\.]/i", '_',pathinfo($filenameWithExt, PATHINFO_FILENAME));
                $extension = $request->file('file_name')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $path = $request->file('file_name')->storeAs($folder, $fileNameToStore);

                $filePath = storage_path('app/'.$path);


                $data = Excel::import(new BulkImport(
                    $request->referral_id,
                    $request->service_id,
                    $request->file_type,
                    $request->form_id
                ), $request->file('file_name'));
                return $this->generateResponse(true,'CSV Uploaded successfully',$data,200);
            }

            return $this->generateResponse(false,'Something Went Wrong!',null,200);
        }catch (\Exception $exception){
            \Log::info($exception->getMessage());
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }

        return $this->generateResponse(false,'something Went Wrong!',null,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCertDate(Request $request)
    {
        $status = 0;
        $delimiter = ",";
        $message = 'Something wrong';
        try {
            $csvData = $request;

            $folder = "csv";
            if($csvData['file_type'] == 1) {
                $folder = "demographic";
            } elseif ($csvData['file_type'] == 2) {
                $folder = "clinical";
            } elseif ($csvData['file_type'] == 3) {
                $folder = "compliance_due";
            } elseif ($csvData['file_type'] == 4) {
                $folder = "previous_md";
            }
            if ($request->hasFile('file_name')) {
                // Get filename with the extension
                $filenameWithExt = $request->file('file_name')->getClientOriginalName();
                //Get just filename
                $filename =  preg_replace("/[^a-z0-9\_\-\.]/i", '_',pathinfo($filenameWithExt, PATHINFO_FILENAME));
                // Get just ext
                $extension = $request->file('file_name')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                // Upload Image
                $path = $request->file('file_name')->storeAs($folder, $fileNameToStore);
                //dd($path);
                //$user->avatar = $fileNameToStore;
                //$user->save();
            }

            $filePath = storage_path('app/'.$path);

            $data = Excel::import(new BulkCertImport(
                    $csvData['referral_id'],
                    $csvData['service_id'],
                    $csvData['file_type'],
                    $csvData['form_id']
                ), $filePath);

            //dd($data);
            //if ($id) {
                $status = 1;
                $message = 'CSV Uploaded successfully';
            //}
        } catch (\Exception $e) {
            $status = 0;
            $message = $e->getMessage() . $e->getLine();
        }

        $response = [
            'status' => $status,
            'message' => $message
        ];

        return response()->json($response, 201);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientReferral  $PatientReferral
     * @return \Illuminate\Http\Response
     */
    public function show(PatientReferral $PatientReferral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientReferral  $PatientReferral
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientReferral $PatientReferral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientReferral  $PatientReferral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientReferral $PatientReferral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientReferral  $PatientReferral
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientReferral $PatientReferral)
    {
        //
    }
}
