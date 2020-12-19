<?php

namespace App\Imports;

use App\Models\PatientReferral;
use App\Models\User;
use Exception;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
ini_set('max_execution_time', '0'); // for infinite time of execution 

HeadingRowFormatter::default('none');

class BulkImport implements ToModel,WithHeadingRow
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
            $user->email = isset($row['email']) ? $row['email'] : NULL;
            $user->password = Hash::make('test123');
            //$user->dob = '2020-10-10';
            if(isset($row['Home Phone']) && !empty($row['Home Phone'])) {
                $user->phone = str_replace('-', '', $row['Home Phone']);
            } elseif(isset($row['Phone2']) && !empty($row['Phone2'])) {
                $user->phone = str_replace('-', '', $row['Phone2']);
            } elseif(isset($row['Home Phone2']) && !empty($row['Home Phone2'])) {
                $user->phone = str_replace('-', '', $row['Home Phone2']);
            }
            $user->assignRole('patient')->syncPermissions(Permission::all());
            $user->save();

            $userId = $user->id;
            return new PatientReferral([
                'referral_id' => $this->referral_id,
                'service_id' => $this->service_id,
                'file_type' => $this->file_type,
                'form_id' => $this->form_id,
                'first_name'     => $row['First Name'],
                'last_name'    => $row['Last Name'],
                'patient_id' => isset($row['Admission ID']) ? $row['Admission ID'] : NULL,
                //'dob' => '2020-10-10',
                'medicaid_number' => isset($row['Medicaid Number']) ? $row['Medicaid Number'] : NULL,
                'medicare_number' => isset($row['Medicare Number']) ? $row['Medicare Number'] : NULL,
                'address_1' => isset($row['Address1']) ? $row['Address1'] : NULL,
                'address_2' => isset($row['Address2']) ? $row['Address2'] : NULL,
                'city' => isset($row['City']) ? $row['City'] : NULL,
                'state' => isset($row['State']) ? $row['State'] : NULL,
                'Zip' => isset($row['State']) ? $row['Zip Code'] : NULL,
                'phone1' => isset($row['Home Phone']) ? $row['Home Phone'] : NULL,
                'phone2' => isset($row['Home Phone2']) ? $row['Home Phone2'] : NULL,
                'user_id' => $userId
            ]);
        } catch(Exception $e) {}
    }
}
