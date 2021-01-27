<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientInsuranceRequest;
use App\Models\PatientInsurance;
use Illuminate\Http\Request;

class PatientInsuranceController extends Controller
{
    public function updateOrCreateInsurance(PatientInsuranceRequest $request){
        if ($request->has('insurance_id')){
            $patientInsurance = PatientInsurance::find($request->insurance_id);
        }else{
            $patientInsurance = new PatientInsurance();
        }
        $patientInsurance->user_id = $request->user_id;
        $patientInsurance->name = $request->name;
        $patientInsurance->patient_id = $request->patient_id;
        $patientInsurance->payer_id = $request->payer_id;
        $patientInsurance->phone = $request->phone;
        $patientInsurance->policy_no = $request->policy_no;
        if ($patientInsurance->save()){
            return $this->generateResponse(true,'Patient Insurance Update or Create Successfully',$patientInsurance,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
    }
}
