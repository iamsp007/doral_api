<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\PatientReferral;
use App\Models\PatientReferralNotSsn;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithValidation;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithProgressBar;

HeadingRowFormatter::default('slug');

class BulkImport implements ToModel, WithHeadingRow, WithValidation
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
    public $errors=[];

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
            $data = $this->setData($row);

            $data['referral_id']=$this->referral_id;
            $data['service_id']=$this->service_id;
            $data['file_type']=$this->file_type;
            $data['form_id']=$this->form_id;

            $patient = PatientReferral::where(function ($q) use ($data){
                $q->where('ssn','=',$data['ssn'])
                    ->orWhere('patient_id','=',$data['patient_id']);
            })
                ->first();
            if ($patient){
                $data = array_filter($data, function($v) { return !is_null($v) && !empty($v); });
                return PatientReferral::updateOrCreate($data);
            }else{
                $user = new User();
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->gender = $this->setGenderAttributes($data['gender']);
                $user->dob = $data['dob'];
                $user->phone = $data['phone1'];
                if (!$this->checkEmailAddressExistsOrNot($data['email'])){
                    $user->email = $data['email'];
                }
                $user->password = Hash::make('doral@123');
                $user->status = '1';
                $user->assignRole('patient')->syncPermissions(Permission::all());
                if ($user->save()){
                    $data['user_id']=$user->id;
                    return PatientReferral::updateOrCreate($data);
                }
            }
        }catch (\Maatwebsite\Excel\Validators\ValidationException $failures){
            $this->errors[]=$row;
        }catch (\Exception $exception){
            $this->errors[]=$row;
        }
    }


    public function rules(): array
    {
        /*return [
            'ssn'=>'required',
            'date_of_birth'=>'required',
        ];*/
        return [];
    }

    public function getErrors(){

        return $this->errors;
    }

    public function setData($row){
        $data=array();
        $data['patient_id']=isset($row['admission_id'])?$row['admission_id']:null;
        $data['ssn']=isset($row['ssn'])?$row['ssn']:null;
        $data['first_name']=isset($row['first_name'])?$row['first_name']:null;
        $data['last_name']=isset($row['last_name'])?$row['last_name']:null;
        $data['middle_name']=isset($row['middle_name'])?$row['middle_name']:null;
        $data['gender']=isset($row['gender'])?$row['gender']:null;
        $data['email']=isset($row['email'])?$row['email']:null;
        $data['dob']=$this->setDob($row);
        $data['medicaid_number']=$this->setMedicaidNumber($row);
        $data['medicare_number']=$this->setMedicareNumber($row);
        $data['working_hour']=$this->setWorkingHourAndPlan($row)[0];
        $data['benefit_plan']=$this->setWorkingHourAndPlan($row)[1];
        $data['caregiver_code']=isset($row['caregiver_code'])?$row['caregiver_code']:null;
//        $data['race']=isset($row['race'])?$row['race']:null;
        $data['ssn']=isset($row['ssn'])?$row['ssn']:null;
        $data['address_1']=$this->setAddress1($row);
        $data['address_2']=$this->setAddress2($row);
        $data['city']=isset($row['city'])?$row['city']:null;
        $data['state']=isset($row['state'])?$row['state']:null;
        $data['county']=isset($row['county'])?$row['county']:null;
        $data['Zip']=isset($row['zip_code'])?$row['zip_code']:null;
        $data['phone1']=$this->setPhone1($row);
        $data['phone2']=$this->setPhone2($row);
        $data['eng_name']=$this->setEmegencyName1($row);
        $data['emg_phone']=$this->setEmegency1Phone($row);
        $data['eng_addres']=$this->setEmegency1Address($row);
        $data['emg_relationship']=$this->setEmegency1RelationShip($row);
        if ($this->setCrtData($row)){
           $data = array_merge($data,$this->setCrtData($row));
        }

        return $data;
    }

    public function setDob($row){
        if (isset($row['date_of_birth'])){
            return Carbon::createFromDate($row['date_of_birth']);
        }
        return null;
    }

    public function setGenderAttributes($value){
        $gender='3';
        if ($value==='Male' || $value==='male'){
            $gender='1';
        }elseif ($value==='Female' || $value==='female'){
            $gender='2';
        }
        return $gender;
    }

    public function checkEmailAddressExistsOrNot($value){
        if ($value){
            $status = User::where('email','=',$value)->first();
            if ($status){
                return true;
            }
        }
        return false;
    }

    public function setAddress2($row){
        $address2 = '';
        if (isset($row['street2'])){
            $address2 = $row['street2'];
        }elseif (isset($row['address2'])){
            $address2 = $row['address2'];
        }
        return $address2;
    }

    public function setAddress1($row){
        $address = '';
        if (isset($row['street1'])){
            $address = $row['street1'];
        }elseif (isset($row['address1'])){
            $address = $row['address1'];
        }elseif (isset($row['address'])){
            $address = $row['address'];
        }
        return $address;
    }

    public function setEmegencyName1($row){
        $emergency1_name = '';
        if (isset($row['emergency1_name'])){
            $emergency1_name = $row['emergency1_name'];
        }
        return $emergency1_name;
    }

    public function setEmegency1RelationShip($row){
        $emergency1_relationship = null;
        if (isset($row['emergency1_relationship'])){
            $emergency1_name = $row['emergency1_relationship'];
        }
        return $emergency1_relationship;
    }

    public function setEmegency1Address($row){
        $emergency1_address = null;
        if (isset($row['emergency1_address'])){
            $emergency1_address = $row['emergency1_address'];
        }
        return $emergency1_address;
    }

    public function setEmegency1Phone($row){
        $emergency1_phone = null;
        if (isset($row['emergency1_phone'])){
            $emergency1_phone = $row['emergency1_phone'];
        }
        return $emergency1_phone;
    }

    public function setWorkingHourAndPlan($row){
        $working_hour = NULL;
        $benefit_plan = NULL;
        if(isset($row['working_hour']) && !empty($row['working_hour'])) {
            $working_hour = $row['working_hour'];
            if($working_hour >=1 && $working_hour <=20) {
                $benefit_plan = 1;
            } else if($working_hour >=21 && $working_hour <=25) {
                $benefit_plan = 2;
            } else if($working_hour >=26 && $working_hour <=30) {
                $benefit_plan = 3;
            } else if($working_hour >=31 && $working_hour <=35) {
                $benefit_plan = 4;
            } else if($working_hour >=36 && $working_hour <=40) {
                $benefit_plan = 5;
            } else {
                $benefit_plan = 1;
            }
        }
        return [$working_hour,$benefit_plan];
    }

    public function setMedicaidNumber($row){
        $medicaid=null;
        if (isset($row['medicaid_number'])){
            $medicaid = $row['medicaid_number'];
        }
        return $medicaid;
    }

    public function setMedicareNumber($row){
        $medicare = '';
        if (isset($row['medicare_number'])){
            $medicare = $row['medicare_number'];
        }
        return $medicare;
    }

    public function setPhone1($row){
        $phone=null;
        if (isset($row['phone'])){
            $phone=$row['phone'];
        }elseif (isset($row['home_phone'])){
            $phone=$row['home_phone'];
        }
        return $phone;
    }

    public function setPhone2($row){
        $phone=null;
        if (isset($row['phone2'])){
            $phone=$row['phone2'];
        }elseif (isset($row['home_phone2'])){
            $phone=$row['home_phone2'];
        }
        return $phone;
    }

    public function setCrtData($row){
        $dataV = [];
        $value = null;
        if (isset($row['cert_period'])){
            $value = $row['cert_period'];
        }elseif (isset($row['certification_period'])){
            $value = $row['certification_period'];
        }
        if ($value){
            $value = str_replace('(','',$value);
            $value = str_replace(')','',$value);
            $value = str_replace(' ','',$value);
            $certPeriod = explode('-',$value);
            if (count($certPeriod)>0){
                $certDateStart = Carbon::parse($certPeriod[0])->format('Y-m-d');
                $certDateEnd = Carbon::parse($certPeriod[1])->format('Y-m-d');
                $certDateNext = Carbon::parse($certPeriod[1])->addDays(100)->format('Y-m-d');
                if ($certDateEnd > Carbon::now()->format('Y-m-d')) {
                    $dataV = [
                        'cert_start_date' => $certDateStart,
                        'cert_end_date' => $certDateEnd,
                        'cert_next_date' => $certDateEnd
                    ];
                    return $dataV;
                }
                $dataV = [
                    'cert_start_date' => $certDateStart,
                    'cert_end_date' => $certDateEnd,
                    'cert_next_date' => $certDateNext
                ];
                return $dataV;
            }
        }
        return null;
    }
}
