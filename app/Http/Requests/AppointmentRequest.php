<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AppointmentRequest extends FormRequest
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
            'book_datetime'=>'required',
            'clinician_ids'=>'required',
        ];
    }

    public function withValidator($validator) {


        $validator->after(function ($validator) {
            if(Auth::user()->hasRole('co-ordinator')){
                $result = User::find($this->patient_id);
                if (!$result) {
                    $validator->errors()->add('book_datetime', 'Invalid Patient Id');
                }
                $this->merge([
                    'patient_id' => $this->patient_id,
                ]);
            }else{
                $this->merge([
                    'patient_id' => Auth::user()->id,
                ]);
            }
            $clinician_ids = explode(',',$this->clinician_ids);

            $clinician = User::whereIn('id',$clinician_ids)
                ->whereHas('roles',function ($q){
                    $q->where('name','clinician');
                })
                ->get();
            if (count($clinician)>=2){
                $this->merge([
                    'provider1' => $clinician[0]->id,
                    'provider2' => $clinician[1]->id,
                ]);
            }else if (count($clinician)===1){
                $this->merge([
                    'provider1' => $clinician[0]->id,
                    'provider2' => $clinician[0]->id,
                ]);
            }else{
                $validator->errors()->add('clinician_ids', 'Invalid Clinician Id');
            }
        });

    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,'Invalid field! try again',null,200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

}
