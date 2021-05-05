<?php

namespace App\Http\Controllers;

use App\Models\PatientReport;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientReportController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        if(isset($input['lab_report_type_id']) && !empty($input['lab_report_type_id'])) {
            $patientReport = PatientReport::where('lab_report_type_id', $input['lab_report_type_id'])->where('user_id' ,$input['user_id'])->get();
        } else {
            $patientReport = PatientReport::find($input['user_id'])->get();
        }

        if ($patientReport) {
            return $this->generateResponse(true, 'Report list', $patientReport, 200);
        }
        return $this->generateResponse(false, 'Report not found', null, 400);
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        $data = array();
        $rules = array(
            // 'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $message =  $validator->errors()->first();
            return $this->generateResponse(false, $message, $data);
        } else {
            try {
                $user = User::gethUserUsingEmail($input['email']);
                if (!$user) {
                    throw new Exception("Email not match with database");
                }
                $userid = $user->id;
                // if ((Hash::check(request('old_password'), $user->password)) == false) {
                //     $message = "Check your old password.";
                //     return $this->generateResponse(false, $message, $data);
                // } else 
                if ((Hash::check(request('new_password'), $user->password)) == true) {
                    $message = "Please enter a password which is not similar then current password.";
                    return $this->generateResponse(false, $message, $data);
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $message = "Password updated successfully.";
                    return $this->generateResponse(true, $message, $data);
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $message = $ex->errorInfo[2];
                } else {
                    $message = $ex->getMessage();
                }
                return $this->generateResponse(false, $message, $data);
            }
        }
    }
}
