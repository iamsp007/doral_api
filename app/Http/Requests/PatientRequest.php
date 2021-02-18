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
            $details = User::with('detail')->find($this->patient_id);
            if (isset($details->detail->address_1) && $details->detail->address_1){
                $address='';
                if ($details->detail->address_1){
                    $address.=$details->detail->address_1;
                }
                if ($details->detail->city){
                    $address.=','.$details->detail->city;
                }
                if ($details->detail->state){
                    $address.=','.$details->detail->state;
                }
                if ($details->detail->country){
                    $address.=','.$details->detail->country;
                }
                if ($details->detail->Zip){
                    $address.=','.$details->detail->Zip;
                }
                $helper = new Helper();
                $response = $helper->getLatLngFromAddress($address);
                if ($response->status==='REQUEST_DENIED'){
                    $latitude=$details->latitude;
                    $longitude=$details->longitude;
                }else{
                    $latitude=$response->results[0]->geometry->location->lat;
                    $longitude=$response->results[0]->geometry->location->lng;
                }
            }else{
                $latitude=$details->latitude;
                $longitude=$details->longitude;
            }

            $this->merge([
                'latitude' => $latitude,
                'longitude'=>$longitude,
                'user_id' => $this->patient_id,
            ]);
        }else{
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
            'latitude'=>'required',
            'longitude'=>'required',
            'reason'=>'required',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $helper = new Helper();
        $response = $helper->generateResponse(false,$validator->errors()->first(),$validator->errors()->messages(),200);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
