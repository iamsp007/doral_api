<?php

namespace App\Http\Controllers;

use App\Events\SendingSMS;
use App\Events\SendVideoMeetingNotification;
use App\Http\Controllers\Zoom\MeetingController;
use App\Http\Requests\PatientRequest;
use App\Http\Requests\SendVideoMeetingNotificationRequest;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\VirtualRoom;

#Import necessary classes from the Vonage API (AKA OpenTok)

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\Role;


class SessionsController extends Controller
{
    /** Creates a new virtual class for teachers
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function createRoom(Request $request)
    {
        try {
            // Get the currently signed-in user
            $user = User::find(Auth::user()->id);
            // Instantiate a new OpenTok object with our api key & secret
            $opentok = new OpenTok(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));

            // Creates a new session (Stored in the Vonage API cloud)
            $session = $opentok->createSession(array('mediaMode' => MediaMode::ROUTED));

            // Create a new virtual class that would be stored in db
            $class = new VirtualRoom();
            // Generate a name based on the name the teacher entered
            $class->name = 'Dr. '.$user->first_name . " " . $user->last_name . " Room - ".$user->id;
            // Store the unique ID of the session
            $class->user_id = $user->id;
            $class->session_id = $session->getSessionId();
            // Save this class as a relationship to the teacher
            $user->myRoom()->save($class);
            // Send the teacher to the classroom where real-time video goes on
            return $this->generateResponse(true,'Class Room Generate successfully!',['id' => $class->id],200);
        }catch (\Exception $exception){
            return $this->generateResponse(false,$exception->getMessage(),null,200);
        }
    }

    public function sendVideoMeetingNotification(SendVideoMeetingNotificationRequest $request){

        $appointment = Appointment::with('patients','provider1Details','provider2Details','meeting','service')
            ->where('id','=',$request->appointment_id)->first()->toArray();
        if ($appointment){

            $appointmentStatusUpdate = Appointment::find($request->appointment_id);
            $appointmentStatusUpdate->status = 'running';
            $appointmentStatusUpdate->save();

            $appointment['role']=$request->role;
            $meeting_data=array(
                'apiKey'=>env('ZOOM_API_KEY'),
                'meetingNumber'=>$appointment['meeting']['meeting_id'],
                'leaveUrl'=>env('WEB_URL').'clinician/end-meeting/'.$request->appointment_id,
                'userName'=>Auth::user()->first_name.' '.Auth::user()->last_name,
                'userEmail'=>Auth::user()->email,
                'passWord'=>$appointment['meeting']['meeting_detail']?$appointment['meeting']['meeting_detail']->password:null,
                'lang'=> 'en-US',
                'china'=> true,
                'role'=> $request->role
            );

            $smsData=array();
            // send notification to provider1
            if ($appointment['provider1_details']){
                if (Auth::user()->id!==$appointment['provider1_details']['id']){
                    if ($appointment['provider1_details']['phone']!==null){
                        $smsData[]=array(
                            'to'=>$appointment['provider1_details']['phone'],
                            'message'=>'Start Your Video Meeting'
                        );
                    }
                    event(new SendVideoMeetingNotification($appointment['provider1_details']['id'],$meeting_data));
                }
            }
            // send notification to provider2
            if ($appointment['provider2_details']){
                if (Auth::user()->id!==$appointment['provider2_details']['id']){
                    if ($appointment['provider2_details']['phone']!==null){
                        $smsData[]=array(
                            'to'=>$appointment['provider2_details']['phone'],
                            'message'=>'Start Your Video Meeting'
                        );
                    }
                    event(new SendVideoMeetingNotification($appointment['provider2_details']['id'],$meeting_data));
                }
            }
            // send notification to provider2
            if ($appointment['patient_id']){
                if (Auth::user()->id!==$appointment['patient_id']){
                    if ($appointment['patients']['phone']!==null){
                        $smsData[]=array(
                            'to'=>$appointment['patients']['phone'],
                            'message'=>'Start Your Video Meeting'
                        );
                    }
                    event(new SendVideoMeetingNotification($appointment['patient_id'],$meeting_data));
                }
            }
            event(new SendingSMS($smsData));
            $data=array(
                'meeting'=>$meeting_data,
                'appointment'=>$appointment
            );
            return $this->generateResponse(true,'Sending Video Calling Message Success',$data,200);
        }
        return $this->generateResponse(false,'Something Went Wrong',null,200);
    }

    public function leaveVideoMeeting(Request $request){
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->generateResponse(false,'Missign data',$validator->errors(),200);
        }

        $appointment = Appointment::find($request->appointment_id);
        if ($appointment){
            $appointment->status = 'completed';
            $appointment->save();
            return $this->generateResponse(true,'Your Video Meeting Is Completed!',$appointment,200);
        }
        return $this->generateResponse(false,'Something Went Wrong',null,200);
    }

    public function startVideoMeetingNotification(Request $request){
        $validator = Validator::make($request->all(), [
            'patient_request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->generateResponse(false,'Missign data',$validator->errors(),200);
        }

        $request->request->add([
            'appointment_id' => $request->patient_request_id,
            'topic' => 'Roadl Start Call',
            'start_time'=>Carbon::now(),
            'agenda'=>'Start Video Agenda'
        ]);

        $meetingController = new MeetingController();
        $resp =  $meetingController->create($request);

        $appointment = \App\Models\PatientRequest::with('meeting','detail')->find($request->patient_request_id);
        if ($appointment){
            event(new SendVideoMeetingNotification($appointment->detail->id,$appointment));
            return $this->generateResponse(true,'Sending Video Calling Message Success',$appointment,200);
        }
        return $this->generateResponse(false,'Something Went Wrong',null,200);
    }
}
