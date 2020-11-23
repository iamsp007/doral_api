<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required',
            'password' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,'The given data is invalid',null,200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
