<?php

namespace App\Http\Controllers;

use App\Models\PatientReport;
use Illuminate\Http\Request;

class PatientReportController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        if(isset($input['lab_report_type_id']) && !empty($input['lab_report_type_id'])) {
            $patientReport = PatientReport::where('lab_report_type_id', $input['lab_report_type_id'])->where('user_id' ,$input['user_id'])->first();
        } else {
            $patientReport = PatientReport::find($input['user_id']);
        }

        if ($patientReport) {
            return $this->generateResponse(true, 'Report list', $patientReport, 200);
        }
        return $this->generateResponse(false, 'Report not found', null, 400);
    }
}
