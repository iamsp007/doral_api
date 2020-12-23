<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CancelAppointmentReasons;
use Exception;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $status = false;
        $data = $services = [];
        $message = "";
        try {
            $respons = Appointment::getAllAppointment();
            //Get Services
            //Get PM/MA
            //Get Co-ordinator
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $message = $respons['message'];
            $data = [
                "appointments" => $respons['data'],
                "services" => $services
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
        //
        $request->validate([
            'title' => 'required',
            'book_datetime' => 'required',
            'start_datetime' => 'required',
            'end_datetime' => 'required',
            'booked_user_id' => 'required',
            'patient_id' => 'required',
            'provider1' => 'required', 'provider2' => 'required',
            'service_id' => 'required'
        ]);
        $status = false;
        $data = [];
        $message = "";
        try {
            $request = json_decode($request->getContent(), true);
            $respons = Appointment::insert($request);
            if (!$respons['status']) {
                throw new Exception($respons['message']);
            }
            $appointment = Appointment::find($respons['data']);
            $message = $respons['message'];
            $data = [
                'appointment' => $appointment
            ];
            return $this->generateResponse($status, $message, $data);
        } catch (\Exception $e) {
            $status = false;
            $message = $e->getMessage();
            return $this->generateResponse($status, $message, $data);
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
            return response()->json([$status, $message, $data]);
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
            $type = $request['type'];
            switch ($type) {
                case 'byDate':
                    # code...
                    break;
                case 'byWeek':
                    # code...
                    break;
                case 'byMonth':
                    # code...
                    break;
                case 'byDateEmployeeId':
                    # code...
                    break;
                case 'byWeekEmployeeId':
                    # code...
                    break;
                case 'byMonthEmployeeId':
                    # code...
                    break;
                case 'byDatePatientId':
                    # code...
                    break;
                case 'byWeekPatientId':
                    # code...
                    break;
                case 'byMonthPatientId':
                    # code...
                    break;
                default:
                    # Get All appointment of current Month
                    break;
            }
            $reasons = CancelAppointmentReasons::all();
            if (!$reasons) {
                throw new Exception("No reasons are found into database");
            }
            $data = [
                'reasons' => $reasons
            ];
            $status = true;
            $message = "Reasons List";
            return response()->json([$status, $message, $data]);
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
