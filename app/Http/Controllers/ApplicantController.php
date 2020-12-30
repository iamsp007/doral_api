<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ApplicantReference;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $status = false;
        $data = $services = [];
        $message = "";
        try {
            $respons = Appointment::getAppointment($id);
            //Get Services
            //Get PM/MA
            //Get Co-ordinator
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                "appointments" => $respons['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $status = false;
        $data = $services = [];
        $message = "";
        try {
            $respons = Appointment::getAppointment($id);
            //Get Services
            //Get PM/MA
            //Get Co-ordinator
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                "appointments" => $respons['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Upcoming Appointment
     */
    public function upcomingPatientAppointment(Request $request)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $request = $request->all();
            if (!$request['patient_id']) {
                throw new Exception("Invalid parameter passed");
            }
            $respons = Appointment::getUpcomingPatientAppointment($request);
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                'appointments' => $respons['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Upcoming Appointment
     */
    public function cancelPatientAppointment(Request $request)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $request = $request->all();
            if (!$request['patient_id']) {
                throw new Exception("Invalid parameter passed");
            }
            $respons = Appointment::getCancelPatientAppointment($request);
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                'appointments' => $respons['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Past Appointment
     */
    public function pastPatientAppointment(Request $request)
    {
        $status = false;
        $data = [];
        $message = "";
        try {
            $request = $request->all();
            if (!$request['patient_id']) {
                throw new Exception("Invalid parameter passed");
            }
            $respons = Appointment::getPastPatientAppointment($request);
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                'appointments' => $respons['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Get Cancel Appointment Reasons
     */
    public function getCancelAppointmentReasons()
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $reasons = CancelAppointmentReasons::all();
            if (!$reasons) {
                throw new Exception("No reasons are found into database");
            }
            $data = [
                'reasons' => $reasons
            ];
            $status = true;
            $message = "Reasons List";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Get Cancel Appointment Reasons
     */
    public function getAppointmentsByDate(Request $request)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $request = $request->all();
            if (!$request['type']) {
                throw new Exception("Invalid type / parameter");
            }
            $appointments = Appointment::getAllAppointment($request);
            $reasons = CancelAppointmentReasons::all();
            if (!$reasons) {
                throw new Exception("No reasons are found into database");
            }
            $data = [
                'reasons' => $reasons,
                'appointments'=>$appointments
            ];
            $status = true;
            $message = "Reasons List";
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }

    /**
     * Cancel The Appointment
     */
    public function cancelAppointment(Request $request)
    {
        $status = 0;
        $data = [];
        $message = 'Something wrong';
        try {
            $request = $request->all();
            if (!$request['appointment_id'] || !$request['reason_id'] || !$request['cancel_user']) {
                throw new Exception("Invalid parameter passed");
            }
            $cancel = Appointment::cancelAppointment($request);
            if (!$cancel['status']) {
                throw new Exception($cancel['message']);
            }
            $message = $cancel['message'];
            $data = [
                'appointments' => $cancel['data']
            ];
            $status = true;
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage() . " " . $e->getLine();
            return $this->generateResponse($status, $message, $data);
        }
    }
}
