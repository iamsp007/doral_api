<?php

namespace App\Models;

use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAssistant extends Model
{
    use HasFactory;

    protected $table='patient_assistant';

    protected $fillable = [
        'patient_referral_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'phone',
        'email'
    ];

    public function patient()
    {
        return $this->belongsTo(PatientReferral::class,'patient_referral_id','id');
    }
}
