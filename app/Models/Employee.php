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

    /**
     * Relation with Appointment
     */
    public function paAppointment()
    {
        return $this->hasMany('App\Models\Appointment', 'provider1', 'id');
    }

    /**
     * Relation with Appointment
     */
    public function Appointment()
    {
        return $this->hasMany('App\Models\Appointment', 'provider2', 'id');
    }

    public static function insert($request)
    {
        try {
            $data = employee::create($request);
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }
    /**
     * Get all Appointment of PA / MA
     */
    public static function getAppoinmentByEmployeeId($id)
    {
        try {
            $resp = Employee::select('id', 'first_name', 'last_name', 'phone', 'email', 'dob')
                ->with('paAppointment')
                ->where('id', $id)
                ->get()
                ->toArray();
            if (!$resp) {
                throw new Exception("No Appointment found");
            }
            dd($resp);
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "All Appointments Of Employee",
                'data' => $resp
            ];
            return $response;
        } catch (\Exception $e) {
            report($e);
            $status =  false;
            $response = [
                'status' => $status,
                'message' => $e->getMessage()
            ];
            return $response;
            exit;
        }
    }
}
