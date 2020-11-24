<?php

namespace App\Http\Controllers;

use App\Models\PatientReferral;
use Illuminate\Http\Request;

class PatientReferralController extends Controller
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
    public function store(Request $request)
    {
        $status = 0;
        $delimiter = ",";
        $message = 'Something wrong';
        try {
            //Post data
            $request = json_decode($request->getContent(), true);
            $csvData = $request['data'];
            $filename = $csvData['file_name'];

            // Get data from CSV
            $filename = public_path('csv') . "/" . $filename;
            if (!file_exists($filename) || !is_readable($filename))
                throw new \ErrorException('Error file not found ');

            $header = null;
            $patients = array();
            if (($handle = fopen($filename, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    if (!$header)
                        $header = $row;
                    else
                        $patients[] = array_combine($header, $row);
                }
                fclose($handle);
            }
            
            foreach ($patients as $patient) {
                $data = array(
                    'referral_id' => $csvData['referral_id'],
                    'first_name' => $patient['First Name'],
                    'middle_name' => $patient['Middle Name'],
                    'last_name' => $patient['Last Name'],
                    'dob' => date('yy-m-d', strtotime($patient['DOB'])),
                    'gender' => $patient['Gender'],
                    'patient_id' => $patient['Patient ID'],
                    'medicaid_number' => $patient['Medicaid Number'],
                    'medicare_number' => $patient['Medicare Number'],
                    'ssn' => $patient['SSN#'],
                    'start_date' => date('yyyy-mm-dd', strtotime($patient['Start Date'])),
                    'from_date' => date('yyyy-mm-dd', strtotime($patient['From Date'])),
                    'to_date' => date('yyyy-mm-dd', strtotime($patient['To Date'])),
                    'address_1' => $patient['Address Line 1'],
                    'address_2' => $patient['Address Line 2'],
                    'city' => $patient['City'],
                    'state' => $patient['State'],
                    'county' => $patient['County'],
                    'Zip' => $patient['Zip'],
                    'phone1' => $patient['Home Phone'],
                    'phone2' => $patient['Home Phone 2'],
                    'eng_name' => $patient['emg Name'],
                    'eng_addres' => $patient['Address1'],
                    'emg_phone' => $patient['Phone 1'],
                    'emg_relationship' => $patient['Relationship'],
                );
                $id = PatientReferral::insert($data);
            }
            
            if ($id) {
                $status = 1;
                $message = 'Company store properly';
            }
        } catch (\Exception $e) {
            $status = 0;
            $message = $e->getMessage(). $e->getLine();
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
