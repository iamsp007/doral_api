<?php

namespace App\Models;

use App\Helpers\Helper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'gender', 'address1', 'address2', 'zip', 'city', 'state', 'country', 'phone', 'home_phone', 'alternate_phone', 'email', 'dob', 'marital_status', 'blood_group', 'photo', 'ssn', 'npi', 'role_id', 'designation_id', 'experience', 'current_job_location', 'language_known', 'user_id', 'emg_first_name', 'emg_last_name', 'emg_address1', 'emg_address2', 'emg_zip', 'emg_phone', 'emg_email', 'join_date', 'employeement_type', 'status', 
    ];


    public static function insert($request)
    {
        try {
            $data = employee::create($request);
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }

    public static function search($condition = array())
    {
        //try{
            $data = employee::select('')
            ->where($condition)
            ->get();
            if(!$data) {
                throw new Exception("Employee data not found");
                return false;
            }
            return $data;
        /*} catch (\Exception $e) {
            
        }*/
    }
}
