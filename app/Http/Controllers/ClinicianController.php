<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClinicianController extends Controller
{
    public function storeSignatures(Request $request)
    {
        $user_id = Auth::user()->id;
        $uploadFolder = 'signature/'.$user_id;

        $applicant = Applicant::where('user_id', $user_id)->first();

        if ($request->file('signature')) {
            $signature = $request->file('signature');
            $signaturePath = $signature->store($uploadFolder, 'public');
            $image_name = basename($signaturePath);

            $applicant->signature = $image_name;
            
        }
        if ($applicant->save()) {
            return $this->generateResponse(true, 'Applicant data!', $applicant->signature_url);
        }

        return $this->generateResponse(false, 'Something went wrong', null);
    }
}
