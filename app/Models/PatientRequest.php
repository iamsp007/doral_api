<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PatientRequest extends Model
{
    use HasFactory;

    public function detail(){

        return $this->hasOne(User::class,'id','user_id');
    }
    public function patientDetail(){

        return $this->hasOne(Patient::class,'user_id','user_id');
    }
    public function ccrm(){
        return $this->hasMany(CCMReading::class,'user_id','user_id');
    }
    public function routes(){
        return $this->hasMany(RoadlInformation::class,'patient_requests_id','id')->with('user');
    }
}
