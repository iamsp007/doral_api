<?php

namespace App\Http\Requests;

use App\Helpers\Helper;
use App\Models\Referral;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PatientRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->patient_id){
            // $details = User::with('demographic')->find($this->patient_id);

            // if (isset($details->demographic->address) && $details->demographic){
            //     $addresses=$details->demographic->address;
            //     $address='';
            //     if (isset($addresses['address1'])){
            //         $address.=$addresses['address1'];
            //     }
            //     if (isset($addresses['address2'])){
            //         $address.=$addresses['address2'];
            //     }
            //     if (isset($addresses['city'])){
            //         $address.=','.$addresses['city'];
            //     }
            //     if (isset($addresses['state'])){
            //         $address.=','.$addresses['state'];
            //     }
            //     if (isset($addresses['country'])){
            //         $address.=','.$addresses['country'];
            //     }
            //     if (isset($addresses['zip'])){
            //         $address.=','.$addresses['zip'];
            //     }
            //     $helper = new Helper();
            //     $response = $helper->getLatLngFromAddress($address);
            //     if ($response->status==='REQUEST_DENIED'){
            //         $latitude=$details->latitude;
            //         $longitude=$details->longitude;
            //     }else{
            //         $latitude=$response->results[0]->geometry->location->lat;
            //         $longitude=$response->results[0]->geometry->location->lng;
            //     }
            // }else{
            //     $latitude=$details->latitude;
            //     $longitude=$details->longitude;
            // }

            $this->merge([
                // 'latitude' => $latitude,
                // 'longitude'=>$longitude,
                'user_id' => $this->patient_id,
            ]);
        } else{
            $this->merge([
                'user_id' => Auth::user()->id,
            ]);
        }

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
            'type_id' => 'required',
            'test_name' => 'required',
            'sub_test_name' => 'required',
            
            //  'latitude'=>'required',
            //  'longitude'=>'required',
            // 'clinician_list_id'=>'required',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
