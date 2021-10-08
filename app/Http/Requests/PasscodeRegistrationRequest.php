<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasscodeRegistrationRequest extends FormRequest
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
            'passcode' => 'required',
            'email' => ['required','email',Rule::unique('users','email')->ignore($this->user)],
        ];
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
