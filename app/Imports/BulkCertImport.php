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

class BulkCertImport implements ToModel,WithHeadingRow
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

            // Check Admission Id with old record
            //dd($row);
            if(isset($row['Admission ID'])) {
                // add prefix 
                // Check Admission Id with old record
                $data = PatientReferral::where('patient_id', 'COT-'.$row['Admission ID'])->first();
                if($data) {
                    if(isset($row['Cert Period'])) {
                        $certPeriod = str_replace('(', '', $row['Cert Period']);
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
                        if ($certDateNext > $date_now) {
                           $dataV = [
                                'cert_start_date' => $certDateStart,
                                'cert_end_date' => $certDateEnd,
                                'cert_next_date' => $certDateNext
                            ];

                            PatientReferral::where('patient_id', $data->patient_id)
                                ->update($dataV);
                        }

                        
                    }

                } else {
                    $user = new User;
                    $user->password = Hash::make('test123');
                    $user->assignRole('patient')->syncPermissions(Permission::all());
                    $user->save();

                    $userId = $user->id;
                    PatientReferral::create([
                        'referral_id' => $this->referral_id,
                        'service_id' => $this->service_id,
                        'file_type' => $this->file_type,
                        'form_id' => $this->form_id,
                        'patient_id' => isset($row['Admission ID']) ? $row['Admission ID'] : NULL,
                        'user_id' => $userId
                    ]);
                }
            
            } 
        }catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}
