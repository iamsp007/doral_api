<?php

namespace App\Http\Controllers;

use App\Models\AssignClinicianToPatient;
use Illuminate\Http\Request;

class AssignClinicianToPatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = AssignClinicianToPatient::with(['patient','clinician'])->get();

        return $this->generateResponse(true, 'clinician list!', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $records = [];

        try {
        
            collect($request->patient_id)->each(function ($item, $key) use (&$records, &$request) {
                $record = [
                    'patient_id' => $item,
                    'clinician_id' => $request->clinician_id
                ];
                $records[] = $record;
            });

            AssignClinicianToPatient::insert($records);

            return $this->generateResponse(true, 'clinician assigned!');

        } catch (\Exception $e) {

            return $this->generateResponse(false, $e->getMessage());
        }
    }

    /**
     * Filter patient data using clinician
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $request)
    {
        $data = AssignClinicianToPatient::with(['patient','clinician'])->where('clinician_id', $request->clinician_id)->get();

        return $this->generateResponse(true, 'clinician list!', $data);
    }

    /**
     * Assign clinician to patient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assign(Request $request)
    {
        $data = AssignClinicianToPatient::where('patient_id', $request->patient_id)->where('clinician_id', $request->clinician_id)->first();
        if ($data) {
            return $this->generateResponse(true, 'clinician list!', $data);
        }
        $assign = new AssignClinicianToPatient();
        $assign->patient_id = $request->patient_id;
        $assign->clinician_id = $request->clinician_id;
        $assign->save();

        return $this->generateResponse(true, 'clinician assigned!', $assign);
    }

    /**
     * Remove clinician from patient
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        $data = AssignClinicianToPatient::where('patient_id', $request->patient_id)->where('clinician_id', $request->clinician_id)->first();

        $data->delete();

        return $this->generateResponse(true, 'clinician unassigned!');
    }
}
