<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PatientRequest extends Model
{
    use HasFactory;

    public function detail(){

        return $this->belongsTo(User::class,'clincial_id','id')->select('id','latitude','longitude','first_name','last_name');
    }

    public function patient(){

        return $this->belongsTo(User::class,'user_id','id')->select('id','latitude','longitude','first_name','last_name');
    }
    public function patientDetail(){

        return $this->hasOne(User::class,'id','user_id')->with('detail');
    }
    public function ccrm(){
        return $this->hasMany(CCMReading::class,'user_id','user_id');
    }
    public function routes(){
        return $this->hasMany(RoadlInformation::class,'patient_requests_id','id')->with('user');
    }
    /**
     * Get Meeting Reasons
     */
    public function meeting()
    {
        return $this->hasOne(VirtualRoom::class, 'appointment_id', 'id');
    }
    /**
     * Get Meeting Reasons
     */
    public function appointmentType()
    {
        return $this->hasOne(AssignAppointmentRoadl::class, 'patient_request_id', 'id');
    }
    /**
     * Get Meeting Reasons
     */
    public function requestType()
    {
        return $this->hasOne(AssignAppointmentRoadl::class, 'patient_request_id', 'id')->with('referral');
    }

    public function getSymptomsAttribute($value){
        if ($value){
            $symtoms = SymptomsMaster::whereIn('id',explode(',',$value))->pluck('name');
            if ($symtoms){
                return implode(',',$symtoms->toArray());
            }
            return '-';
        }
        return '-';
    }

    public function getDiesesAttribute($value){
        if ($value){
            $data = DiesesMaster::whereIn('id',explode(',',$value))->pluck('name');
            if ($data){
                return implode(',',$data->toArray());
            }
            return '-';
        }
        return '-';
    }
}
