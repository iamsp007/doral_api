<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
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
            'patient_id'=>'required',
            'medication'=>'required',
            'dose'=>'required',
            'form'=>'required',
            'route'=>'required',
            'amount'=>'required',
            'class'=>'required',
            'frequency'=>'required',
            'startdate'=>'required|date',
            'orderdate'=>'required|date',
            'taughtdate'=>'required|date',
            'discontinuedate'=>'required|date',
            'discountinueorderdate'=>'required|date',
            'preferredPharmacy'=>'required',
            'comment'=>'required',
            'status'=>'required',
        ];
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
