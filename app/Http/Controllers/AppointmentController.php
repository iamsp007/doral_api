<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Zoom\MeetingController;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\CancelAppointmentReasons;
use App\Models\EmployeeLeaveManagement;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            $message = $e->getMessage()." ".$e->getLine();
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
    public function store(AppointmentRequest $request)
    {
        $start_time = Carbon::createFromDate($request->book_datetime)->format('Y-m-d H:i:s');
        $end_time = Carbon::createFromDate($request->book_datetime)->addMinute(30)->format('Y-m-d H:i:s');
        $patient = User::with('detail')->find($request->patient_id);

        $appointment = new Appointment();
        $appointment->title = 'Your Book Appointment Is '.$patient->first_name.' '.$patient->last_name;
        $appointment->book_datetime = $request->book_datetime;
        $appointment->start_datetime = $start_time;
        $appointment->end_datetime = $end_time;
        $appointment->booked_user_id = $request->user_id;
        $appointment->patient_id = $request->patient_id;
        $appointment->provider1 = $request->provider1;
        $appointment->provider2 = $request->provider2;
        $appointment->service_id = isset($patient->detail)?$patient->detail->service_id:1;
        if ($appointment->save()){
//            $request->request->add([
//                'appointment_id' => $appointment->id,
//                'topic' => $appointment->title,
//                'start_time'=>$appointment->start_datetime,
//                'agenda'=>'Agenda'
//            ]);

//            $request->request->add(
//                [
//                    'appointment_id' => $appointment->id,
//                    'topic' => $appointment->title,
//                    'start_time'=>$appointment->start_datetime,
//                    'agenda'=>'Agenda'
//                ]
//            );

            $meetingController = new MeetingController();
            $resp =  $meetingController->createMeeting([
                'appointment_id' => $appointment->id,
                'topic' => $appointment->title,
                'start_time'=>$appointment->start_datetime,
                'agenda'=>'Agenda'
            ]);
            return $this->generateResponse(true,'Your Appointment book Successfully!',null,200);
        }
        return $this->generateResponse(false,'Something Went Wrong!',null,200);
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

    public function getClinicianTimeSlots(Request $request){
        $validator = Validator::make(
            $request->all(),
            ['date' => 'required|date']
        );
        if ($validator->fails())
        {
           return $this->generateResponse(false,'Invalid date',null,200);
        }
        $weekDays = Carbon::createFromDate($request->date)->dayOfWeek;

        $usersList = User::with('roles','leave')
            ->whereHas('roles',function($q){
                $q->where('name','=','clinician');
            })
            ->leftJoin('appointments','appointments.provider1','!=','users.id')
            ->whereRaw('NOT FIND_IN_SET("'.$weekDays.'",week_off)')
            ->get();
        $minTime = collect($usersList)->min('work_start_time');
        $maxTime = collect($usersList)->max('work_end_time');
        $timeStamp = $this->getTimeStamp($minTime,$maxTime,30);

        $aminTime = Carbon::createFromDate(collect($usersList)->min('start_datetime'))->format('H:i');
        $amaxTime = Carbon::createFromDate(collect($usersList)->min('end_datetime'))->format('H:i');
        $result = array_diff(array($aminTime,$amaxTime),$timeStamp);
        dd($result);




        $data=array();
        $count=0;
        foreach ($timeStamp as $key=>$item) {

            $time = Carbon::createFromDate($request->date.' '.$item)->format('H:i:s');
            $usersList = User::with('roles','appointment')
                ->leftJoin('appointments','appointments.provider1','!=','users.id')
                ->whereHas('roles',function($q){
                    $q->where('name','=','clinician');
                })
                ->whereRaw('NOT FIND_IN_SET("'.$weekDays.'",week_off)')
                ->where(function ($q) use ($time){
                    $q->whereTime('work_start_time','<=',$time)
                        ->whereTime('work_end_time','>=',$time);
                })
                ->get();

            if (count($usersList)>0){
                $appointmentTimeStamps=array();
                foreach ($usersList as $value) {
                    $start = Carbon::createFromDate($value->start_datetime)->format('H:i:s');
                    $end = Carbon::createFromDate($value->end_datetime)->format('H:i:s');
                    $timeStamps = $this->getTimeStamp($start,$end,30);
                    $appointmentTimeStamps[]=$timeStamps;
                }
                dd($appointmentTimeStamps);

                $ids = collect($usersList)->pluck('id');

                if ($count===0){
                    $prev_time = Carbon::createFromDate($request->date.' '.$item)->subMinute('30')->format('Y-m-d H:i');
                    $data[]=array(
                        'id'=>implode(', ', $ids->toArray()),
                        'count'=>count($usersList),
                        'time'=>$prev_time
                    );
                }
                $count++;


                $data[]=array(
                    'id'=>implode(', ', $ids->toArray()),
                    'count'=>count($usersList),
                    'time'=>Carbon::createFromDate($request->date.' '.$item)->format('Y-m-d H:i')
                );
            }
        }
        return $this->generateResponse(true,'get clinician list',collect($data),200);

    }

    public function getTimeStamp($start,$end,$duration){
        $start_time = Carbon::parse($start)->format('H:i');
        $end_time = Carbon::parse($end)->format('H:i');

        $i=0;
        $time[$i] = $start_time;
        while(strtotime($start_time) <= strtotime($end_time)){
            $start = $start_time;
            $end = date('H:i',strtotime('+'.$duration.' minutes',strtotime($start_time)));
            $start_time = date('H:i',strtotime('+'.$duration.' minutes',strtotime($start_time)));
            $i++;
            if(strtotime($start_time) <= strtotime($end_time)){
                $time[$i] = $start;
                $time[$i] = $end;
            }
        }
        return $time;
    }
}
