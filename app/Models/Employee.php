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
        'employee_ID', 'driving_license', 'user_id', 'status', 'partner_id', 
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
