<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\PatientEmergencyContact;
use Illuminate\Http\Request;

class ClinicianRegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeallapplicantDetail(Request $request)
    {
        $input = $request->all();
       
        if($request['key'])
        $applicant = Applicant::firstOrNew(['user_id' => $input['user_id']]);
        $action = "Added";
        if ($applicant->exists) {
            $action = "Updated";
        }
        $applicant->$request['key'] = $request['key'];
        $applicant->save();
      
        return $this->generateResponse(true, 'Family Detail ' . $action . ' Suucessfully.', $input, 200);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmergencyContact(Request $request)
    {
        $input = $request->all();

        $address = [
            'apt_building' => $input['apt_building'],
            'address1' => $input['address1'],
            'address2' => $input['address2'],
            'zip_code' => $input['zip_code'],
            'city' => $input['city'],
            'state' => $input['state']
        ];

        PatientEmergencyContact::create([
            'name' => $input['name'],
            'phone1' => $input['phone'],
            'relation' => $input['relation'],
            'address' => $address,
            'lives_with_patient' => $input['lives_with_patient']
        ]);

        return $this->generateResponse(true, 'Emergency contact detail added suucessfully.', $input, 200);
    }

    /* Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getApplicantDetail($user_id)
    {
        $applicant = Applicant::where('user_id', $user_id);

        return $this->generateResponse(true, 'Family Detail', $applicant, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getEmergencyContact($user_id)
    {
        $patientEmergencyContact = PatientEmergencyContact::where('user_id', $user_id);

        return $this->generateResponse(true, 'Emergency contact detail ', $patientEmergencyContact, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeApplicantDetail(Request $request)
    {
        $input = $request->all();
       
        $applicant = Applicant::firstOrNew(['user_id' => $input['user_id']]);
        $action = "Added";
        if ($applicant->exists) {
            $action = "Updated";
        }
        $applicant->phone = $input['phone'];
        $applicant->home_phone =$input['home_phone'];
        $applicant->save();
      
        return $this->generateResponse(true, 'Family Detail ' . $action . ' Suucessfully.', $input, 200);
    }
}
