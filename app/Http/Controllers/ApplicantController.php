<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ApplicantReference;
use App\Models\Education;
use Illuminate\Http\Request;
use Exception;

class ApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = false;
        $data = [];
        $message = "Applicants are not available.";
        try {
            $response = Applicant::with(['referances', 'state', 'city'])->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }

    /**
     * Step one a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepOne(Request $request)
    {
        try {
            $request->validate([
                'applicant_name' => 'required',
                'ssn' => 'required',
                'phone' => 'required',
                'date' => 'required'
            ]);
            $applicant = new Applicant();
            $applicant->user_id = $request->user()->id;
            $applicant->applicant_name = $request->applicant_name;
            $applicant->other_name = $request->other_name;
            $applicant->ssn = $request->ssn;
            $applicant->phone = $request->phone;
            $applicant->home_phone = $request->home_phone;
            $applicant->date = $request->date;
            $applicant->us_citizen = $request->us_citizen;
            $applicant->immigration_id = $request->immigration_id;

            if ($applicant->save()){
                $status = true;
                $message = "Success! Please complete step two.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, []);
        }
    }

    /**
     * Step two a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepTwo(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'address_line_1' => 'required',
                'city' => 'required',
                'state' => 'required',
                'zip' => 'required',
                'address_life' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->address_line_1 = $request->address_line_1;
            $applicant->address_line_2 = $request->address_line_2;
            $applicant->city = $request->city;
            $applicant->state = $request->state;
            $applicant->zip = $request->zip;
            $applicant->address_life = $request->address_life;

            if ($applicant->save()){
                $status = true;
                $message = "Success! Please complete step three.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, []);
        }
    }

    /**
     * Step three a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepThree(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'referance.*.referance_name' => 'required',
                'referance.*.reference_address' => 'required',
                'referance.*.reference_phone' => 'required',
                'referance.*.reference_relationship' => 'required',
                'bonded' => 'required',
                'refused_bond' => 'required',
                'convicted_crime' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->bonded = $request->bonded;
            $applicant->refused_bond = $request->refused_bond;
            $applicant->convicted_crime = $request->convicted_crime;

            if ($applicant->save()){
                $records = [];
                collect($request->referance)->each(function ($item, $key) use (&$records, &$request) {
                    $record = [
                        'applicant_id' => $request->applicant_id,
                        'referance_name' => $item['referance_name'],
                        'reference_address' => $item['reference_address'],
                        'reference_phone' => $item['reference_phone'],
                        'reference_relationship' => $item['reference_relationship']
                    ];
                    $records[] = $record;
                });
                ApplicantReference::insert($records);
                $status = true;
                $message = "Success! Please complete step four.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, []);
        }
    }

    /**
     * Step four a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stepFour(Request $request)
    {
        try {
            $request->validate([
                'applicant_id' => 'required',
                'emergency_name' => 'required',
                'emergency_address' => 'required',
                'emergency_phone' => 'required',
                'emergency_relationship' => 'required'
            ]);
            $applicant = Applicant::findOrFail($request->applicant_id);
            $applicant->emergency_name = $request->emergency_name;
            $applicant->emergency_address = $request->emergency_address;
            $applicant->emergency_phone = $request->emergency_phone;
            $applicant->emergency_relationship = $request->emergency_relationship;

            if ($applicant->save()){
                $status = true;
                $message = "Successfully completed all steps.";
                return $this->generateResponse($status, $message, $applicant, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, []);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function addressLife()
    {
        $status = true;
        $message = "Address Life";
        $data = config('common.address_life');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function relationship()
    {
        $status = true;
        $message = "Relationship";
        $data = config('common.relationship');
        return $this->generateResponse($status, $message, $data);
    }

    /**
     * Education a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function education(Request $request)
    {
        try {
            $request->validate([
                'medical_institute_name' => 'required',
                'medical_institute_address' => 'required',
                'medical_institute_city' => 'required',
                'medical_institute_state' => 'required',
                'medical_institute_year_started' => 'required',
                'medical_institute_year_completed' => 'required',
                'residency_institute_name' => 'required',
                'residency_institute_address' => 'required',
                'residency_institute_city' => 'required',
                'residency_institute_state' => 'required',
                'residency_institute_year_started' => 'required',
                'residency_institute_year_completed' => 'required'
            ]);
            $education = new Education();
            $education->user_id = $request->user()->id;

            $education->medical_institute_name = $request->medical_institute_name;
            $education->medical_institute_address = $request->medical_institute_address;
            $education->medical_institute_city = $request->medical_institute_city;
            $education->medical_institute_state = $request->medical_institute_state;
            $education->medical_institute_year_started = $request->medical_institute_year_started;
            $education->medical_institute_year_completed = $request->medical_institute_year_completed;

            $education->residency_institute_name = $request->residency_institute_name;
            $education->residency_institute_address = $request->residency_institute_address;
            $education->residency_institute_city = $request->residency_institute_city;
            $education->residency_institute_state = $request->residency_institute_state;
            $education->residency_institute_year_started = $request->residency_institute_year_started;
            $education->residency_institute_year_completed = $request->residency_institute_year_completed;

            $education->fellowship_institute_name = $request->fellowship_institute_name;
            $education->fellowship_institute_address = $request->fellowship_institute_address;
            $education->fellowship_institute_city = $request->fellowship_institute_city;
            $education->fellowship_institute_state = $request->fellowship_institute_state;
            $education->fellowship_institute_year_started = $request->fellowship_institute_year_started;
            $education->fellowship_institute_year_completed = $request->fellowship_institute_year_completed;

            if ($education->save()){
                $status = true;
                $message = "Successfully stored education data.";
                return $this->generateResponse($status, $message, $education, 200);
            }
            return $this->generateResponse(false, 'Something Went Wrong!', [], 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, []);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEducation()
    {
        $status = false;
        $data = [];
        $message = "Educations are not available.";
        try {
            $response = Education::with(['user', 'medicalInstituteState', 'medicalInstituteCity', 'residencyInstituteState', 'residencyInstituteCity', 'fellowshipInstituteState', 'fellowshipInstituteCity'])->get();
            if (!$response) {
                throw new Exception($message);
            }
            $status = true;
            $message = "All Applicants.";
            return $this->generateResponse($status, $message, $response, 200);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage()." ".$e->getLine();
            return $this->generateResponse($status, $message, $data, 200);
        }
    }
}
