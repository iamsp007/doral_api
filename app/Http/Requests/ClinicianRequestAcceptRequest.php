<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ClinicianRequestAcceptRequest extends FormRequest
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
            'request_id'=>'required'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,'The given data was Invalid',null,200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);

    }
}
