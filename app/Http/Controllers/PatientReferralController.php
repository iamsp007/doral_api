<?php

namespace App\Http\Controllers;

use App\Models\patientReferral;
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
        $data = array();
        try {
            $patientReferral = patientReferral::all()->toArray();
            if (!$patientReferral) {
                throw new Exception("No Referance Patients are registered");
            }
            $data = [
                'patientReferral' => $patientReferral
            ];
            return $this->generateResponse(true, 'Referance Patients!', $data);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
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
    public function store(Request $request)
    {
        $status = 0;
        $delimiter = ",";
        $message = 'Something wrong';
        try {
            //Post data
            //$request = json_decode($request->getContent(), true);
            $csvData = $request;

            //upload file
            if ($request->hasFile('file_name')) {
                // Get filename with the extension
                $filenameWithExt = $request->file('file_name')->getClientOriginalName();
                //Get just filename
                $filename =  preg_replace("/[^a-z0-9\_\-\.]/i", '_',pathinfo($filenameWithExt, PATHINFO_FILENAME));
                // Get just ext
                $extension = $request->file('file_name')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                // Upload Image
                $path = $request->file('file_name')->storeAs('csv', $fileNameToStore);
                //dd($path);
                //$user->avatar = $fileNameToStore;
                //$user->save();
            }

            // Get data from CSV
            if (!\Storage::disk('local')->exists($path))
                throw new \ErrorException('File not found');
            $filePath = storage_path('app/'.$path);
            $header = null;
            $patients = array();
            if (($handle = fopen($filePath, 'r')) !== false) {
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
                    'dob' => date('yy-m-d', strtotime($patient['Date of Birth'])),
                    'gender' => $patient['Gender'],
                    //'patient_id' => $patient['Patient ID'],
                    //'medicaid_number' => $patient['Medicaid Number'],
                    //'medicare_number' => $patient['Medicare Number'],
                    'ssn' => $patient['SSN'],
                    'start_date' => date('yy-m-d', strtotime($patient['Hire Date'])),
                    //'from_date' => date('yy-m-d', strtotime($patient['From Date'])),
                    //'to_date' => date('yy-m-d', strtotime($patient['To Date'])),
                    'address_1' => $patient['Street1'],
                    //'address_2' => $patient['Address Line 2'],
                    'city' => $patient['City'],
                    'state' => $patient['State'],
                    'county' => $patient['Country of Birth'],
                    'Zip' => $patient['Zip Code'],
                    'phone1' => $patient['Home Phone'],
                    'phone2' => $patient['Phone2'],
                    //'eng_name' => $patient['emg Name'],
                    //'eng_addres' => $patient['Address1'],
                    //'emg_phone' => $patient['Phone 1'],
                    'emg_relationship' => $patient['Marital Status'],
                );
                $id = patientReferral::insert($data);                
            }

            if ($id) {
                $status = 1;
                $message = 'CSV Uploaded successfully';
            }
        } catch (\Exception $e) {
            $status = 0;
            $message = $e->getMessage() . $e->getLine();
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
     * @param  \App\Models\patientReferral  $patientReferral
     * @return \Illuminate\Http\Response
     */
    public function show(patientReferral $patientReferral)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\patientReferral  $patientReferral
     * @return \Illuminate\Http\Response
     */
    public function edit(patientReferral $patientReferral)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\patientReferral  $patientReferral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, patientReferral $patientReferral)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\patientReferral  $patientReferral
     * @return \Illuminate\Http\Response
     */
    public function destroy(patientReferral $patientReferral)
    {
        //
    }
}
