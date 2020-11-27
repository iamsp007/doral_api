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
        'first_name', 'last_name', 'gender', 'address1', 'address2', 'zip', 'phone', 'email', 'dob', 'ssn', 'npi', 'role_id', 'designation_id', 'emg_first_name', 'emg_last_name', 'emg_address1', 'emg_address2', 'emg_zip', 'emg_phone', 'emg_email', 'join_date', 'Employeement _type', 'status', 'user_id'
    ];


    public static function insert($request)
    {
        try {
            $data = employee::create($request);
            return $data;
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
