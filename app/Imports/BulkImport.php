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
        try {

            $patient = PatientReferral::where(['ssn'=>$row['ssn']])->first();
            if ($patient){
                $user = User::find($patient->user_id);
            }else{
                $user = new User();
                $patient = new PatientReferral();
            }
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

                return PatientReferral::updateOrCreate(
                   [
                       'user_id'=>$user->id,
                       'referral_id'=>$this->referral_id,
                       'service_id'=>$this->service_id,
                       'file_type'=>$this->file_type,
                       'form_id'=>$this->form_id,
                       'first_name'=>$row['first_name'],
                       'last_name'=>$row['last_name'],
                       'middle_name'=>isset($row['middle_name'])?$row['middle_name']:null,
                       'gender'=>$row['gender'],
                       'ssn' => $row['ssn'],
                       'dob'=>Carbon::createFromDate($row['date_of_birth']),
                       'phone1'=>isset($row['phone2'])?$row['phone2']:null,
                       'phone2'=>isset($row['phone2'])?$row['phone2']:null,
                       'address_1'=>$address,
                       'address_2'=>$address2,
                       'eng_name'=>$emergency1_name,
                   ]);
            }
            \Log::info(123456);

        } catch(Exception $e) {
            \Log::info($e);
        }
    }


    public function rules(): array
    {
        return [
            'ssn'=>'required',
            'date_of_birth'=>'required',
        ];
    }
}
