<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class PatientRequestOtpVerifyRequest extends FormRequest
{
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
            'id'=>'required|exists:patient_requests,id',
            'otp'=>'required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->validateOtpVerify($this->otp,$this->id)){
                $helper = new Helper();
                $response = $helper->generateResponse(false,'Your Otp Is Invalid:');
                throw new \Illuminate\Validation\ValidationException($validator, $response);
            }
        });
    }

    public function validateOtpVerify($otp,$id){
        $patientRequest  = \App\Models\PatientRequest::find($id);
        return $otp===$patientRequest->otp;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
