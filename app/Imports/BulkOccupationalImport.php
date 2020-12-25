<?php

namespace App\Imports;

use App\Models\PatientOccupational;
use App\Models\User;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
ini_set('max_execution_time', '0'); // for infinite time of execution 

HeadingRowFormatter::default('none');

class BulkOccupationalImport implements ToModel,WithHeadingRow
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
       $this->referral_id = $rid;
       $this->service_id = $sid;
       $this->file_type = $ftype;
       $this->form_id = $fid;
    }
    
    public function model(array $row)
    {

        try {
            $user = new User;
            $user->first_name = isset($row['First Name']) ? $row['First Name'] : NULL;
            $user->last_name = isset($row['Last Name']) ? $row['Last Name'] : NULL;
            $user->email = isset($row['Email']) ? $row['Email'] : NULL;
            $user->password = Hash::make('test123');
            $user->dob = isset($row['Date of Birth']) ? date('Y-m-d', strtotime($row['Date of Birth'])) : NULL;
            //$user->dob = '2020-10-10';
            if(isset($row['Phone2']) && !empty($row['Phone2'])) {
                $user->phone = str_replace('-', '', $row['Phone2']);
            } elseif(isset($row['Emergency1 Phone1']) && !empty($row['Emergency1 Phone1'])) {
                $user->phone = str_replace('-', '', $row['Emergency1 Phone1']);
            }
            $user->assignRole('patient')->syncPermissions(Permission::all());
            $user->save();
            
            $userId = $user->id;
            //dd($userId);
            return new PatientOccupational([
                'referral_id' => $this->referral_id,
                'service_id' => $this->service_id,
                'file_type' => $this->file_type,
                'form_id' => $this->form_id,
                'first_name'     => $row['First Name'],
                'last_name'    => $row['Last Name'],
                'middle_name'    => isset($row['Middle Name']) ? $row['Middle Name'] : NULL,
                'caregiver_code' => isset($row['Caregiver Code']) ? $row['Caregiver Code'] : NULL,
                'ssn' => isset($row['SSN']) ? $row['SSN'] : NULL,
                'gender' => isset($row['Gender']) ? $row['Gender'] : NULL,
                'dob' => isset($row['Date of Birth']) ? date('Y-m-d', strtotime($row['Date of Birth'])) : NULL,
                'address_1' => isset($row['Street1']) ? $row['Street1'] : NULL,
                'phone1' => isset($row['Emergency1 Phone1']) ? $row['Emergency1 Phone1'] : NULL,
                'phone2' => isset($row['Phone2']) ? $row['Phone2'] : NULL,
                'email' => isset($row['Email']) ? $row['Email'] : NULL,
                'user_id' => $userId
            ]);
        } catch(Exception $e) {
            //dd($e->getMessage());
        }
    }
}
