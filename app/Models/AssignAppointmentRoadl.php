<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignAppointmentRoadl extends Model
{
    use HasFactory;

    public function requests(){
        return $this->hasOne(PatientRequest::class,'id','patient_request_id')->with('detail');
    }
}
