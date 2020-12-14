<?php

namespace App\Http\Controllers;

use App\Models\PatientReferral;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class PatientReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $data = array();
        try {
            $patientReferral = patientReferral::where('service_id', $id)
            ->get();
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
            $csvData = $request;

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
            $patients = array_filter($patients);
            $patients = array_values($patients);
            foreach ($patients as $patient) {
                \Log::info($csvData);
                if($patient['SSN'] != '') {

                    try {
                    // User Add
                    $user = new User;
                    $user->first_name = isset($patient['First Name']) ? $patient['First Name'] : NULL;
                    $user->last_name = isset($patient['Last Name']) ? $patient['Last Name'] : NULL;
                    $user->email = isset($patient['email']) ? $patient['email'] : NULL;
                    $user->password = Hash::make('test123');
                    $user->dob = isset($patient['Date of Birth']) ? date('yy-m-d', strtotime($patient['Date of Birth'])) : NULL;
                    if(isset($patient['Home Phone']) && !empty($patient['Home Phone'])) {
                        $user->phone = str_replace('-', '', $patient['Home Phone']);
                    } elseif(isset($patient['Phone2']) && !empty($patient['Phone2'])) {
                        $user->phone = str_replace('-', '', $patient['Phone2']);
                    }
                    $user->assignRole('patient')->syncPermissions(Permission::all());
                    $user->save();

                    $userId = $user->id;
                    //dd($userId);

                    $data = array(
                        'referral_id' => $csvData['referral_id'],
                        'service_id' => $csvData['service_id'],
                        'file_type' => $csvData['file_type'],
                        'user_id' => $userId,
                        'first_name' => isset($patient['First Name']) ? $patient['First Name'] : NULL,
                        'middle_name' => isset($patient['Middle Name']) ? $patient['Middle Name'] : NULL,
                        'last_name' => isset($patient['Last Name']) ? $patient['Last Name'] : NULL,
                        'dob' => isset($patient['Date of Birth']) ? date('yy-m-d', strtotime($patient['Date of Birth'])) : NULL,
                        'gender' => isset($patient['Gender']) ? $patient['Gender'] : NULL,
                        'patient_id' => isset($patient['Patient ID']) ? $patient['Patient ID'] : NULL,
                        'medicaid_number' => isset($patient['Medicaid Number']) ? $patient['Medicaid Number'] : NULL,
                        'medicare_number' => isset($patient['Medicare Number']) ? $patient['Medicare Number'] : NULL,
                        'ssn' => $patient['SSN'],
                        'start_date' => isset($patient['Hire Date']) ? date('yy-m-d', strtotime($patient['Hire Date'])) : NULL,
                        'from_date' => isset($patient['From Date']) ? date('yy-m-d', strtotime($patient['From Date'])) : NULL,
                        'to_date' => isset($patient['To Date']) ? date('yy-m-d', strtotime($patient['To Date'])) : NULL,
                        'address_1' => isset($patient['Street1']) ? $patient['Street1'] : NULL,
                        'address_2' => isset($patient['Address Line 2']) ? $patient['Address Line 2'] : NULL,
                        'city' => isset($patient['City']) ? $patient['City'] : NULL,
                        'state' => isset($patient['State']) ? $patient['State'] : NULL,
                        'county' => isset($patient['Country of Birth']) ? $patient['Country of Birth'] : NULL,
                        'Zip' => isset($patient['Zip Code']) ? $patient['Zip Code'] : NULL,
                        'phone1' => isset($patient['Home Phone']) ? $patient['Home Phone'] : NULL,
                        'phone2' => isset($patient['Phone2']) ? $patient['Phone2'] : NULL,
                        'email' => isset($patient['Email']) ? $patient['Email'] : NULL,
                        'eng_name' => isset($patient['emg Name']) ? $patient['emg Name'] : NULL,
                        'eng_addres' => isset($patient['Address1']) ? $patient['Address1'] : NULL,
                        'emg_phone' => isset($patient['Phone 1']) ? $patient['Phone 1'] : NULL,
                        'emg_relationship' => isset($patient['Marital Status']) ? $patient['Marital Status'] : NULL,
                    );

                    $id = patientReferral::insert($data);
                    }
                    catch(Exception $e) {
//                        echo $e->getMessage()."<br>";
                    }
                }
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
