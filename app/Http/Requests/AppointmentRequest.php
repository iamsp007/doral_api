<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'book_datetime'=>'required',
            'start_datetime'=>'required',
            'end_datetime'=>'required',
            'booked_user_id'=>'required',
            'patient_id'=>'required',
            'provider1'=>'required','provider2'=>'required',
            'service_id'=>'required',
            'appointment_url'=>'required',
        ];
    }
}
