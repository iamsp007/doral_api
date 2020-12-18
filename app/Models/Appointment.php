<?php

namespace App\Models;

use App\Helpers\Helper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_datetime', 'start_datetime', 'end_datetime', 'booked_user_id', 'patient_id', 'provider1', 'provider2', 'service_id', 'appointment_url'
    ];
    /**
     * Get Patient details
     */
    public function patients()
    {
        return $this->belongsTo('App\Models\Patient', 'patient_id', 'id');
    }
    /**
     * Get Booked details
     */
    public function bookedDetails()
    {
        return $this->belongsTo('App\Models\Employee', 'booked_user_id', 'id');
    }
    /**
     * Get Provider1 details
     */
    public function provider1Details()
    {
        return $this->belongsTo('App\Models\Employee', 'provider1', 'id');
    }
    /**
     * Get Provider2 details
     */
    public function provider2Details()
    {
        return $this->belongsTo('App\Models\Employee', 'provider2', 'id');
    }
    /**
     * Insert data into Patient table
     */
    public static function getAllAppointment()
    {
        $status = 0;
        try {
            $resp = Appointment::with(['bookedDetails' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
                ->with(['patients' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->get()
                ->toArray();
            if (!$resp) {
                throw new Exception("Appointments are not available");
            }
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "All Appointments",
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
    /**
     * Insert data into Patient table
     */
    public static function insert($request)
    {
        $status = 0;
        try {
            $resp = Appointment::create($request);
            $data = $resp->id;
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "Appointment inserted sucessfully",
                'data' => $data
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
    /**
     * Update patient information based on id
     */
    public static function updateAppointment($id, $request)
    {
        try {
            $data = Appointment::where('id', $id)->update($request);
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }
    /**
     * Upcoming Patient Appointment
     */
    public static function getUpcomingPatientAppointment($request)
    {
        $status = 0;
        try {
            $currentDate = Helper::curretntDate();
            //\DB::enableQueryLog();;
            $resp = Appointment::with(['bookedDetails' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
                ->with(['patients' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->where([
                    ['start_datetime', '>=', $currentDate],
                    ['patient_id', '=', $request['patient_id']]
                ])
                ->get()
                ->toArray();
            //dd(\DB::getQueryLog());
           
            $data = $resp;
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "Upcoming Appoinments",
                'data' => $data
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
    /**
     * Cancel Patient Appointment
     */
    public static function getCancelPatientAppointment($request)
    {
        $status = 0;
        try {
            $currentDate = Helper::curretntDate();
            //\DB::enableQueryLog();;
            $resp = Appointment::with(['bookedDetails' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
                ->with(['patients' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->where([
                    ['status', '=', 'cancel'],
                    ['patient_id', '=', $request['patient_id']]
                ])
                ->get()
                ->toArray();
            //dd(\DB::getQueryLog());
           
            $data = $resp;
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "Upcoming Appoinments",
                'data' => $data
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
    /**
     * Cancel Patient Appointment
     */
    public static function getPastPatientAppointment($request)
    {
        $status = 0;
        try {
            $currentDate = Helper::curretntDate();
            //\DB::enableQueryLog();;
            $resp = Appointment::with(['bookedDetails' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
                ->with(['patients' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->where([
                    ['start_datetime', '<=', $currentDate],
                    ['status', '=', 'completed'],
                    ['patient_id', '=', $request['patient_id']]
                ])
                ->get()
                ->toArray();
            //dd(\DB::getQueryLog());
           
            $data = $resp;
            $status =  true;
            $response = [
                'status' => $status,
                'message' => "Upcoming Appoinments",
                'data' => $data
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
