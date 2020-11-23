<?php

namespace App\Http\Controllers;

use App\Events\SendClinicianPatientRequestNotification;
use App\Http\Requests\CCMReadingRequest;
use App\Models\CCMReading;
use App\Models\PatientRequest;
use App\Http\Requests\PatientRequest as PatientRequestValidation;
use App\Notifications\BroadCastNotification;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use mysql_xdevapi\Exception;
use phpDocumentor\Reflection\Types\Object_;

class PatientRequestController extends Controller
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
    public function store(PatientRequestValidation $request)
    {
        try {
            $patient = new PatientRequest();
            $patient->user_id = $request->user_id;
            $patient->latitude = $request->latitude;
            $patient->longitude = $request->longitude;
            $patient->reason = $request->reason;
            if ($patient->save()){
                event(new SendClinicianPatientRequestNotification($patient));
                return $this->generateResponse(true,'Add Request Successfully!');
            }
            return $this->generateResponse(false,'Something Went Wrong!');
        }catch (Exception $exception){
            return $this->generateResponse(false,$exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PatientRequest $patientRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PatientRequest  $patientRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PatientRequest $patientRequest)
    {
        //
    }

    public function ccmReading(CCMReadingRequest $request){

        $ccmReadingModel = new CCMReading();
        $ccmReadingModel->user_id = $request->user_id;
        $ccmReadingModel->reading_type = $request->reading_type;
        $ccmReadingModel->reading_value = $request->reading_value;
        if ($ccmReadingModel->save()){
            return $this->generateResponse(true,'CCM Reading Success!');
        }
        return $this->generateResponse(false,'Something Went Wrong!');
    }
}
