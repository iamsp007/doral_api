<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;

class ClinicianRegisterController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFamilyDetail(Request $request)
    {
        $input = $request->all();
       
        $applicant = Applicant::firstOrNew(['user_id' => $input['user_id']]);
        $action = "Added";
        if ($applicant->exists) {
            $action = "Updated";
        }
        $applicant->family_detail = $input['family_detail'];
        $applicant->save();
      
        return $this->generateResponse(true, 'Family Detail ' . $action . ' Suucessfully.', $input, 200);
    }
}
