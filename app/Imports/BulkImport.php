<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\PatientReferral;
use App\Models\PatientReferralNotSsn;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Date;
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

    public function __construct($rid, $sid, $ftype, $fid) {
//        \Log::info($sid);
       $this->referral_id = $rid;
       $this->service_id = $sid;
       $this->file_type = $ftype;
       $this->form_id = $fid;
    }



    public function model(array $row)
    {
        $record = [];
        try {

            if( (isset($row['ssn']) && !empty($row['ssn'])) && (isset($row['date_of_birth']) && !empty($row['date_of_birth']))) {
                $patient = PatientReferral::where(['ssn'=>$row['ssn']])->first();
                if ($patient){
                    $user = User::find($patient->user_id);
                    $address = $patient->address1;
                    if (isset($row['street1'])){
                        $address = $row['street1'];
                    }elseif (isset($row['address1'])){
                        $address = $row['address1'];
                    }

                    $address2 = $patient->address2;
                    if (isset($row['street2'])){
                        $address2 = $row['street2'];
                    }elseif (isset($row['address2'])){
                        $address2 = $row['address2'];
                    }

                    $emergency1_name = $patient->eng_name;
                    if (isset($row['emergency1_name'])){
                        $emergency1_name = $row['emergency1_name'];
                    }

                    $working_hour = $patient->working_hour;
                    $benefit_plan = $patient->benefit_plan;
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
                    $dataV = [];
                    if(isset($row['cert_period'])) {
                        $certPeriod = str_replace('(', '', $row['cert_period']);
                        $certPeriod = str_replace(')', '', $certPeriod);
                        $certPeriod = str_replace(' ', '', $certPeriod);
                        $certDate = explode('-', $certPeriod);

                        $certDateStart = strtotime($certDate[0]);
                        $certDateStart = date('Y-m-d', $certDateStart);

                        $certDateEnd = strtotime($certDate[1]);
                        $certDateEnd = date('Y-m-d', $certDateEnd);
                        // Next date
                        $certDateNext = $certDate[1];
                        $certDateNext = date('Y-m-d', strtotime($certDateNext. ' + 180 days'));

                        $date_now = date("Y-m-d"); // this format is string comparable
                        if ($certDateEnd > $date_now) {
                           $dataV = [
                                'cert_start_date' => $certDateStart,
                                'cert_end_date' => $certDateEnd,
                                'cert_next_date' => $certDateEnd
                            ];
                        } else {
                            $dataV = [
                                'cert_start_date' => $certDateStart,
                                'cert_end_date' => $certDateEnd,
                                'cert_next_date' => $certDateNext
                            ];
                        }
                    } else {
                        $dataV = [
                            'cert_start_date' => $patient->cert_start_date,
                            'cert_end_date' => $patient->cert_end_date,
                            'cert_next_date' => $patient->cert_next_date
                        ];
                    }
                    $record = [
                             'user_id'=>$user->id,
                             'referral_id'=>$this->referral_id,
                             'service_id'=>$this->service_id,
                             'file_type'=>$this->file_type,
                             'form_id'=>isset($this->form_id)?$this->form_id:$patient->form_id,
                             'first_name'=>isset($row['first_name']) ? $row['first_name'] : $patient->first_name,
                             'last_name'=>isset($row['last_name']) ? $row['last_name'] : $patient->last_name,
                             'middle_name'=>isset($row['middle_name'])?$row['middle_name']:$patient->middle_name,
                             'gender'=>isset($row['gender'])?$row['gender']:$patient->gender,
                             'email' => isset($row['email'])?$row['email']:$patient->email,
                             'dob'=>Carbon::createFromDate($row['date_of_birth']),
                             'phone1'=>isset($row['phone2'])?$row['phone2']:$patient->phone1,
                             'phone2'=>isset($row['phone2'])?$row['phone2']:$patient->phone2,
                             'address_1'=>$address,
                             'address_2'=>$address2,
                             'eng_name'=>$emergency1_name,
                             'patient_id'=>isset($row['admission_id'])?$row['admission_id']:$patient->patient_id,
                             'caregiver_code' => isset($row['caregiver_code'])?$row['caregiver_code']:$patient->caregiver_code,
                             'city' => isset($row['city'])?$row['city']:$patient->city,
                             'state' => isset($row['state'])?$row['state']:$patient->state,
                             'Zip' => isset($row['zip_code'])?$row['zip_code']:$patient->Zip,
                             'county' => isset($row['county'])?$row['county']:$patient->county,
                             'working_hour' => $working_hour,
                             'benefit_plan' => $benefit_plan
                         ];
                    if(count($dataV) > 0) {     
                      $record = array_merge($record, $dataV); 
                    }    
                    PatientReferral::where('id', $patient->id)
                            ->update($record);
                }else{
                    $user = new User();
                    $patient = new PatientReferral();
                    $user->first_name = $row['first_name'];
                    $user->last_name = $row['last_name'];
                    if (strtolower($row['gender'])==='male'){
                        $user->gender = '1';
                    }elseif (strtolower($row['gender'])==='female'){
                        $user->gender = '2';
                    }else{
                        $user->gender = '3';
                    }
                    \Log::info($user);
                    $user->dob = Carbon::createFromDate($row['date_of_birth']);

                    if (isset($row['email']) && !empty($row['email'])){
                        if (!User::where(['email'=>$row['email']])->first()){
                            $user->email = $row['email'];
                        }
                    }

                    $user->password = Hash::make('doral@123');
                    if (isset($row['phone2']) && is_numeric($row['phone2'])){
                        $user->phone = $row['phone2'];
                    }
                    $user->assignRole('patient')->syncPermissions(Permission::all());

                    if ($user->save()){

                        $address = '';
                        if (isset($row['street1'])){
                            $address = $row['street1'];
                        }elseif (isset($row['address1'])){
                            $address = $row['address1'];
                        }

                        $address2 = '';
                        if (isset($row['street2'])){
                            $address2 = $row['street2'];
                        }elseif (isset($row['address2'])){
                            $address2 = $row['address2'];
                        }

                        $emergency1_name = '';
                        if (isset($row['emergency1_name'])){
                            $emergency1_name = $row['emergency1_name'];
                        }

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
                        PatientReferral::updateorcreate(
                           [
                               'user_id'=>$user->id,
                               'referral_id'=>$this->referral_id,
                               'service_id'=>$this->service_id,
                               'file_type'=>$this->file_type,
                               'form_id'=>isset($this->form_id)?$this->form_id:NULL,
                               'first_name'=>$row['first_name'],
                               'last_name'=>$row['last_name'],
                               'middle_name'=>isset($row['middle_name'])?$row['middle_name']:null,
                               'gender'=>isset($row['gender'])?$row['gender']:null,
                               'email' => isset($row['email'])?$row['email']:null,
                               'ssn' => $row['ssn'],
                               'dob'=>Carbon::createFromDate($row['date_of_birth']),
                               'phone1'=>isset($row['phone2'])?$row['phone2']:null,
                               'phone2'=>isset($row['phone2'])?$row['phone2']:null,
                               'address_1'=>$address,
                               'address_2'=>$address2,
                               'eng_name'=>$emergency1_name,
                               'patient_id'=>isset($row['admission_id'])?$row['admission_id']:null,
                               'caregiver_code' => isset($row['caregiver_code'])?$row['caregiver_code']:null,
                               'city' => isset($row['city'])?$row['city']:null,
                               'state' => isset($row['state'])?$row['state']:null,
                               'Zip' => isset($row['zip_code'])?$row['zip_code']:null,
                               'county' => isset($row['county'])?$row['county']:null,
                               'working_hour' => $working_hour,
                               'benefit_plan' => $benefit_plan
                           ]);
                    }
                    \Log::info(123456);
                  }
          } else {
              $patientRefNotSsn = new PatientReferralNotSsn();
              $patientRefNotSsn->referral_id = $this->referral_id;
              $patientRefNotSsn->patient_id = isset($row['admission_id'])?$row['admission_id']:null;
              $patientRefNotSsn->caregiver_code = isset($row['caregiver_code'])?$row['caregiver_code']:null;
              $patientRefNotSsn->save();
          }
          //dd($record);
          //PatientReferral::insert($record);
        } catch(Exception $e) {
            \Log::info($e);
            //dd($e->getMessage());
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
}
