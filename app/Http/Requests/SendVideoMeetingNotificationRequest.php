<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Http\Controllers\Zoom\MeetingController;
use App\Models\Appointment;
use App\Models\VirtualRoom;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SendVideoMeetingNotificationRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => Auth::user()->id,
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'appointment_id' => 'required|exists:appointments,id'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checkStatus = Appointment::find($this->appointment_id);
            $status=['open','running'];
            if (!in_array($checkStatus->status,$status)){
                $helper = new Helper();
                $response = $helper->generateResponse(false,'Your Appointment Is :'.$checkStatus->status);
                throw new \Illuminate\Validation\ValidationException($validator, $response);
            }

            $meeting = VirtualRoom::where('appointment_id','=',$this->appointment_id)->first();
            $meetingController = new MeetingController();
            $request = request();
            $checkStatusMeeting = $meetingController->get($request,$meeting->meeting_id);
            $role=1;
            if($checkStatusMeeting['success']===true){
                if ($checkStatusMeeting['data']['status']==="started"){
                    $role=0;
                }
            }
            $this->merge([
                'role' => $role,
            ]);
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,$validator->errors()->first(),null,200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
