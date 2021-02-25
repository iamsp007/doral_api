<?php

namespace App\Http\Controllers;

use App\Models\PatientReferral;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Validators\ValidationException;
use Spatie\Permission\Models\Permission;
use Excel;
use App\Imports\BulkImport;
use App\Imports\BulkCertImport;
use App\Models\PatientAssistant;
use App\Models\FailRecodeImport;

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
            $patientReferral = patientReferral::with('detail', 'service', 'filetype', 'mdforms', 'plans')
            ->where('service_id', $id)
            ->whereNotNull('first_name')
            ->get();
            if (!$patientReferral) {
                throw new Exception("No Referance Patients are registered");
            }
            $data = [
                'patientReferral' => $patientReferral
            ];
            //return $this->generateResponse(true, 'Referance Patients!', $data);
            return $this->generateResponse(true,'Referance Patients!',$patientReferral,200);
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
         $this->validate($request, ['file_name' => 'required', 'file_type' => 'required', 'referral_id' => 'required', 'service_id' => 'required', ]);

        try
        {

            $folder = 'csv';
            if ($request->file_type === 1)
            {
                $folder = "demographic";
            }
            elseif ($request->file_type === 2)
            {
                $folder = "clinical";
            }
            elseif ($request->file_type === 3)
            {
                $folder = "compliance_due";
            }
            elseif ($request->file_type === 4)
            {
                $folder = "previous_md";
            }

            // upload file
            if ($request->hasFile('file_name'))
            {
                $filenameWithExt = $request->file('file_name')
                    ->getClientOriginalName();
                $filename = preg_replace("/[^a-z0-9\_\-\.]/i", '_', pathinfo($filenameWithExt, PATHINFO_FILENAME));
                $extension = $request->file('file_name')
                    ->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $path = $request->file('file_name')
                    ->storeAs($folder, $fileNameToStore);

                $filePath = storage_path('app/' . $path);

                $import = new BulkImport($request->referral_id, $request->service_id, $request->file_type, $request->form_id, $filenameWithExt);
                $import->Import($request->file('file_name'));

                return $this->generateResponse(true, 'CSV Uploaded successfully', $import, 200);
            }

            return $this->generateResponse(false, 'Something Went Wrong!', null, 200);

        }
        catch(\Exception $exception)
        {
            \Log::info($exception->getMessage());
            return $this->generateResponse(false, $exception->getMessage() , null, 200);
        }

        return $this->generateResponse(false, 'something Went Wrong!', null, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCertDate(Request $request)
    {
        $status = 0;
        $delimiter = ",";
        $message = 'Something wrong';
        try {
            $csvData = $request;

            $folder = "csv";
            if($csvData['file_type'] == 1) {
                $folder = "demographic";
            } elseif ($csvData['file_type'] == 2) {
                $folder = "clinical";
            } elseif ($csvData['file_type'] == 3) {
                $folder = "compliance_due";
            } elseif ($csvData['file_type'] == 4) {
                $folder = "previous_md";
            }
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
                $path = $request->file('file_name')->storeAs($folder, $fileNameToStore);
                //dd($path);
                //$user->avatar = $fileNameToStore;
                //$user->save();
            }

            $filePath = storage_path('app/'.$path);

            $data = Excel::import(new BulkCertImport(
                    $csvData['referral_id'],
                    $csvData['service_id'],
                    $csvData['file_type'],
                    $csvData['form_id']
                ), $filePath);
            //dd($data);
            //if ($id) {
                $status = 1;
                $message = 'CSV Uploaded successfully';
            //}
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

    public function storePatient(Request $request)
    {
        $this->validate($request,[
            'first_name'=>'required',
            'middle_name'=>'required',
            'last_name'=>'required',
            'gender'=>'required',
            'dob'=>'required',
            'ssn'=>'required',
            'medicare_number'=>'required',
            'medicaid_number'=>'required',
            'address_1'=>'required',
            'state'=>'required',
            'city'=>'required',
            'Zip'=>'required'
        ]);
        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->gender = $request->gender;
            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->password = \Hash::make('patient@doral');
            $user->save();

            $patient = new PatientReferral();
            $patient->user_id = $user->id;
            $patient->first_name = $request->first_name;
            $patient->middle_name = $request->middle_name;
            $patient->last_name = $request->last_name;
            $patient->gender = $request->gender;
            $patient->dob = date('Y-m-d', strtotime($request->dob));
            $patient->ssn = $request->ssn;
            $patient->medicare_number = $request->medicare_number;
            $patient->medicaid_number = $request->medicaid_number;
            $patient->address_1 = $request->address_1;
            $patient->state = $request->state;
            $patient->city = $request->city;
            $patient->Zip = $request->Zip;

            $patient->enrollment = $request->enrollment;
            $patient->creation_date = (isset($request->enrollment) && isset($request->creation_date) && $request->enrollment == 'existing_patient') ? date('Y-m-d', strtotime($request->creation_date)) : null;
            $patient->services = $request->services;
            $patient->insurance = $request->insurance;
            $patient->hmo_to_mlts = (isset($request->insurance) && $request->insurance == 'hmo') ? $request->hmo_to_mlts : null;
            $patient->save();

            if ($request->services == 'cdpap') {
                if ($request->first_names) {
                    $pa1 = new PatientAssistant();
                    $pa1->patient_referral_id = $patient->id;
                    $pa1->first_name = $request->first_names;
                    $pa1->middle_name = $request->middle_names;
                    $pa1->last_name = $request->last_names;
                    $pa1->gender = $request->gender1;
                    $pa1->phone = $request->phones;
                    $pa1->email = $request->emails;
                    $pa1->save();
                }
                if ($request->first_names) {
                    $pa2 = new PatientAssistant();
                    $pa2->patient_referral_id = $patient->id;
                    $pa2->first_name = $request->first_namess;
                    $pa2->middle_name = $request->middle_namess;
                    $pa2->last_name = $request->last_namess;
                    $pa2->gender = $request->gender2;
                    $pa2->phone = $request->phoness;
                    $pa2->email = $request->emailss;
                    $pa2->save();
                }
            }

            return $this->generateResponse(true,'Patient added',$patient,200);
        }catch (\Exception $exception){
            \Log::info($exception->getMessage());
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function faileRecode(Request $request) {
       $data = array();
       $id = $request->id;
        try {
            $faileRecode = FailRecodeImport::where('service_id', $id)
            ->select('id','row','file_name','attribute','errors')
            ->get();
            if (!$faileRecode) {
                throw new Exception("No Referance Patients are registered");
            }
            $data = [
                'faileRecode' => $faileRecode
            ];
            //return $this->generateResponse(true, 'Referance Patients!', $data);
            return $this->generateResponse(true,'Failedrecode!',$faileRecode,200);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
    }

    public function viewfaileRecode(Request $request) {
       $data = array();
       $id = $request->id;
        try {
            $faileRecode = FailRecodeImport::where('id', $id)
            ->select('values')
            ->first();
            $data = array();

            $data_send = json_decode($faileRecode->values);
             array_push($data,$data_send);

            if (!$faileRecode) {
                throw new Exception("No Referance Patients are registered");
            }
            // $data = [
            //     'faileRecode' => $data_send
            // ];
            //return $this->generateResponse(true, 'Referance Patients!', $data);
            return $this->generateResponse(true,'Failedrecode!',$data,200);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return $this->generateResponse(false, $message, $data);
        }
    }
}
