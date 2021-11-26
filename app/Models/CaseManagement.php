<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\PatientReferral;
use DB;

class CaseManagement extends Model
{
    use HasFactory,Notifiable,HasRoles;

    protected $guard = 'supervisor';

    protected $table='case_management';
    protected $primaryKey='id';

    protected $fillable=['patient_id','clinician_id'];

    public function service(){
        return $this->hasOne(Services::class,'id','service_id');
    }

    public function filetype(){
        return $this->hasOne(FileTypeMaster::class,'id','file_type');
    }

    public function clinician(){
        return $this->hasOne(User::class,'id','clinician_id');
    }

    public static function getAccepted(){
        return PatientReferral::select("patient_referrals.*","patient_referrals.id as pr_id","case_management.id as id","case_management.clinician_id as clinician_id", \DB::raw("CONCAT(patient_referrals.first_name,' ',patient_referrals.last_name) as full_name"))->with('service','filetype')->Join('case_management', 'case_management.patient_id', '=', 'patient_referrals.id')->where('status','accept')->get();
    }

}
