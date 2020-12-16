<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoadlSelectedDiesesRequest;
use App\Models\Patient;
use App\Models\PatientRequest;
use Exception;
use Illuminate\Http\Request;

class PatientController extends Controller
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
     * Search patient by name / Email / phone
     *
     * @return \Illuminate\Http\Response
     */
    public function getAppoinment($keyword)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $res = Patient::searchByEmailNamePhone($keyword);
            dd($res);
            $status = true;
            $message = $res['message'];
            $data = [
                'data' => $res['data']
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getFile(). $e->getMessage(). $e->getLine();
            return $this->generateResponse($status, $message, $data);
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
    public function store($request)
    {
        $data = Patient::insert($request);
        return $data;
    }

    /**
     * StoreInformation is store pateint detailed information based on different steps
     * This services is call from App only. There is not web API for patent registation
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeInfomation($step, Request $request)
    {
        $status = false;
        $resp = [];
        if ($step == 1) {
            $request->validate([
                'ssn' => 'required',
                'medicaid_number' => 'numeric',
                'medicare_number' => 'numeric',
                'address1' => 'required',
                'address2' => 'required',
                'zip' => 'required',
                'service_key' => 'required'
            ]);
        }

        try {
            $request = json_decode($request->getContent(), true);
            if (!$step) {
                throw new Exception("Invalid parameter are required");
            }
            if (!$request['id']) {
                throw new Exception("Invalid parameter Id are required");
            }
            $id = $request['id'];
            unset($request['id']);
            $patient = Patient::find($id);
            if (!$patient) {
                throw new Exception("Patient are not found into database");
            }
            switch ($step) {
                case '1':
                    $data = Patient::updatePatient($id, $request);
                    if ($data) {
                        $status = true;
                        $message = "Patient information saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                case '2': // Insert services
                    $data = Patient::updateServices($id, $request);
                    if ($data) {
                        $status = true;
                        $message = "Patient serives saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                case '3': // Insert Insurance
                    $data = Patient::updateInsurance($id, $request);
                    if ($data) {
                        $status = true;
                        $message = "Patient Insurance saved Successfully";
                        return $this->generateResponse($status, $message, $resp);
                    }
                    break;
                default:
                    throw new Exception("Invalid Parameters");
                    break;
            }
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $resp);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientDieses  $patient
     * @return \Illuminate\Http\Response
     */
    public function roadlSelectedDisease(Request $request)
    {
        $patientRequest = PatientRequest::find($request->patient_request_id);
        if ($patientRequest){
            $patientRequest->dieses=$request->dieses;
            $patientRequest->symptoms=$request->symptoms;
            $patientRequest->is_parking=$request->is_parking;
            $patientRequest->status='active';
            $patientRequest->save();
            return $this->generateResponse(true, 'Detail Update Successfully!', $patientRequest,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
