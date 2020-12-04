<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReferral extends Model
{
    use HasFactory;
    protected $fillable = array('referral_id', 'service_id', 'file_type', 'first_name', 'last_name', 'dob', 'middle_name', 'gender', 'patient_id', 'medicaid_number', 'medicare_number', 'ssn', 'start_date', 'from_date', 'to_date', 'address_1', 'address_2', 'city', 'state', 'county', 'Zip', 'phone1', 'phone2', 'eng_name', 'eng_addres', 'emg_phone', 'emg_relationship');
    
    protected $guarded = [];

    /**
     * Insert the User data from the Employee / Patient 
     * 
     */
    public static function insert($request)
    {

        try {
            $data = PatientReferral::create($request);
            return $data->id;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
