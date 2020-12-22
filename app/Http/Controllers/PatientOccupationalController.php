<?php

namespace App\Http\Controllers;

use App\Models\PatientOccupational;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Excel;
use App\Imports\BulkOccupationalImport;

class PatientOccupationalController extends Controller
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
            $patient = PatientOccupational::where('service_id', $id)
            ->whereNotNull('first_name')
            ->get();
            if (!$patient) {
                throw new Exception("No Referance Patients are registered");
            }
            return $this->generateResponse(true,'Referance Patients!',$patient,200);
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
    public function storeOccupational(Request $request)
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
            $data = Excel::import(new BulkOccupationalImport(
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
            //dd($message);
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
