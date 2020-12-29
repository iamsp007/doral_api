<?php

namespace App\Imports;

use App\Models\PatientReferral;
use App\Models\PatientReferralNotSsn;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
ini_set('max_execution_time', '0'); // for infinite time of execution

HeadingRowFormatter::default('none');

class BulkImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public $referral_id = null;
    public $service_id = null;
    public $file_type = null;
    public $form_id = null;



    public function __construct($rid, $sid, $ftype, $fid) {
//        \Log::info($sid);
       $this->referral_id = $rid;
       $this->service_id = $sid;
       $this->file_type = $ftype;
       $this->form_id = $fid;
    }

    public function model(array $row)
    {
        try {
            $a = array_map('trim', array_keys($row));
            $b = array_map('trim', $row);
            $stripResults = array_combine($a, $b);
            if (
                (isset($stripResults['SSN']) && !empty($stripResults['SSN'])) &&
                (isset($stripResults['Date of Birth']) && !empty($stripResults['Date of Birth']))
            ){

                $patient = PatientReferral::where(['ssn'=>$stripResults['SSN']])->first();
                if ($patient){
                    $user = User::find($patient->user_id);
                }else{
                    $patient = new PatientReferral();
                    $user = new User();
                }

                $data=array();
                foreach ($stripResults as $key=>$value) {
                    $user->password=Hash::make('doral@123');

                    if (strtolower($key)==='first name'){
                        $user->first_name=$value;
                        $patient->first_name=$value;
                    }elseif (strtolower($key)==='last name'){
                        $user->last_name=$value;
                        $patient->last_name=$value;
                    }elseif (strtolower($key)==='middle name'){
                        $patient->middle_name=$value;
                    }elseif (strtolower($key)==='gender'){
                        $user->gender=$value;
                        $patient->gender=$value;
                    }elseif (strtolower($key)==='medicaid number' || strtolower($key)==='medicaid number'){
                        $patient->medicaid_number=$value;
                    }elseif (strtolower($key)==='medicare number' || strtolower($key)==='Medicare Number'){
                        $patient->medicare_number=$value;
                    }elseif (strtolower($key)==='date of birth'){
                        $user->dob=$value;
                        $patient->dob=$value;
                    }elseif (strtolower($key)==='caregiver code'){
                        $patient->caregiver_code = $value;
                    }elseif (strtolower($key)==='SSN'){
                        $patient->ssn = $value;
                    }elseif (strtolower($key)==='admission id'){
                        $patient->patient_id = $value;
                    }elseif (strtolower($key)==='street1'){
                        $patient->address_1 = $value;
                    }elseif (strtolower($key)==='street2'){
                        $patient->address_2 = $value;
                    }elseif (strtolower($key)==='city'){
                        $patient->city = $value;
                    }elseif (strtolower($key)==='state'){
                        $patient->state = $value;
                    }elseif (strtolower($key)==='zip code'){
                        $patient->Zip = $value;
                    }elseif (strtolower($key)==='phone2' || strtolower($key)==='home phone'){
                        $user->phone=is_numeric($value)?$value:null;
                        $patient->phone=is_numeric($value)?$value:null;
                    }elseif (strtolower($key)==='emergency1 name'){
                        $patient->eng_name = $value;
                    }elseif (strtolower($key)==='emergency1 relationship'){
                        $patient->emg_relationship = $value;
                    }elseif (strtolower($key)==='emergency1 address'){
                        $patient->eng_addres = $value;
                    }elseif (strtolower($key)==='emergency1 phone1'){
                        $patient->emg_phone = $value;
                    }elseif (strtolower($key)==='emergency1 phone2'){
                        $patient->phone2 = $value;
                    }elseif (strtolower($key)==='email'){
                        $user->email=$value;
                        $patient->email=$value;
                    }
                }

                $patient->referral_id = $this->referral_id;
                $patient->service_id = $this->service_id;
                $patient->file_type = $this->file_type;
                $patient->form_id = $this->form_id;
                $user->save();
                $patient->user_id = $user->id;
                return $patient->save();
            }
            return false;
        } catch(Exception $e) {

        }
    }

}
