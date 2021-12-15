<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class PasscodeandfingerLoginRequest extends FormRequest
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
        if ($this->login_type == 'passcode') {
            return [
                'passcode' => 'required',
            ];
        } elseif ($this->login_type == 'fingerPrint') {
            return [
                'finger_print' => 'required',
            ];
        }
        
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($validator->fails()) {
            $helper = new Helper();
            $response = $helper->generateResponse(false,$validator->errors()->first(),null,400);
            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }
    }
}
