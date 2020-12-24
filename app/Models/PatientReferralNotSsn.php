<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReferralNotSsn extends Model
{
    use HasFactory;
    protected $fillable = array('referral_id', 'patient_id', 'caregiver_code');

    protected $guarded = [];
    
    public static function insert($request)
    {

        try {
            $data = PatientReferralNotSsn::create($request);
            return $data->id;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
