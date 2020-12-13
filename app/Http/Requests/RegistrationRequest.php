<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required|string',
            'email' => 'string|email|unique:users',
            'password' => 'required|string',
            'dob' => 'required|date',
            'phone' => 'numeric'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checkNumber = User::where('phone','=',$this->phone)->first();
            if ($checkNumber!==null) {
                $helper = new Helper();
                $response = $helper->generateResponse(false,'Your Phone Number Already Registered!');
                throw new \Illuminate\Validation\ValidationException($validator, $response);
            }
            $checkEmail = User::where('email','=',$this->email)->first();
            if ($checkEmail!==null) {
                $helper = new Helper();
                $response = $helper->generateResponse(false,'Your Email Address Already Registered!');
                throw new \Illuminate\Validation\ValidationException($validator, $response);
            }
        });
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,'The given data is invalid',null,200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
