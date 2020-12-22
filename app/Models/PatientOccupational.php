<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientOccupational extends Model
{
    use HasFactory;
    protected $fillable = array('referral_id', 'service_id', 'file_type', 'user_id','first_name', 'last_name', 'dob', 'middle_name', 'gender', 'caregiver_code', 'medicaid_number', 'medicare_number', 'ssn', 'start_date', 'from_date', 'to_date', 'address_1', 'address_2', 'city', 'state', 'county', 'Zip', 'phone1', 'phone2', 'email', 'eng_name', 'eng_addres', 'emg_phone', 'emg_relationship', 'form_id');

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

    public function detail(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function service(){
        return $this->hasOne(services::class,'id','service_id');
    }

    public function filetype(){
        return $this->hasOne(FileTypeMaster::class,'id','file_type');
    }
}
