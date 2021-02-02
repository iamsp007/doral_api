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
        'title', 'appointment_url', 'book_datetime', 'start_datetime', 'end_datetime', 'booked_user_id', 'patient_id', 'provider1', 'provider2', 'service_id', 'appointment_url','cancel_user','reason_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s',
    ];
    /**
     * Get Patient details
     */
    public function patients()
    {
        return $this->hasOne(User::class, 'id', 'patient_id')->with('detail');
    }
    /**
     * Get Booked details
     */
    public function bookedDetails()
    {
        return $this->hasOne(User::class, 'id', 'booked_user_id');
    }
    /**
     * Get Provider1 details
     */
    public function provider1Details()
    {
        return $this->hasOne(User::class, 'id', 'provider1');
    }
    /**
     * Get Provider2 details
     */
    public function provider2Details()
    {
        return $this->hasOne(User::class, 'id', 'provider2');
    }
    /**
     * Get Cancel Appointment Reasons
     */
    public function cancelAppointmentReasons()
    {
        return $this->hasOne(CancelAppointmentReasons::class, 'id', 'reason_id');
    }

    public function service()
    {
        return $this->hasOne(Services::class, 'id', 'service_id');
    }

    public function filetype()
    {
        return $this->hasOne(FileTypeMaster::class, 'id', 'file_type');
    }
    /**
     * Get Meeting Reasons
     */
    public function meeting()
    {
        return $this->hasOne(VirtualRoom::class, 'appointment_id', 'id');
    }
    /**
     * Get Meeting Reasons
     */
    public function cancelByUser()
    {
        return $this->hasOne(User::class, 'id', 'cancel_user');
    }
    /**
     * Get Roadl Request
     */
    public function roadl()
    {
        return $this->hasMany(AssignAppointmentRoadl::class, 'appointment_id', 'id');
    }
    /**
     * Get All Appointment
     */
    public static function getAppointment($id)
    {
        $status = 0;
        try {
            $resp = Appointment::with(['bookedDetails' => function ($q) {
                $q->select('first_name', 'last_name', 'id');
            }])
                ->with(['patients', 'meeting', 'service', 'filetype'])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->where('id', $id)
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
                ->with(['patients', 'meeting', 'service', 'filetype'])
                ->with(['provider1Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->with(['provider2Details' => function ($q) {
                    $q->select('first_name', 'last_name', 'id');
                }])
                ->where([
                    ['start_datetime', '>=', $currentDate],
                    ['patient_id', '=', $request['patient_id']],
                    ['status', 'open']
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
                ->with(['patients', 'meeting', 'service', 'filetype'])
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
                'message' => "Cancel Appoinments",
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
                ->with(['patients', 'meeting', 'service', 'filetype'])
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
                'message' => "Past Appoinments",
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
     * Cancel Appointment
     */
    public static function cancelAppointment($request)
    {
        $status = 0;
        try {
            $appointment = Appointment::findOrFail($request['appointment_id']);
            $appointment->reason_id = $request['reason_id'];
            $appointment->reason_notes = isset($request['reason_notes']) ? $request['reason_notes'] : null;
            $appointment->cancel_user = $request['cancel_user'];
            $appointment->status = 'cancel';

            $appointment->save();
            $response = [
                'status' => true,
                'message' => "Appoinment cancelled.",
                'data' => $appointment
            ];
            return $response;
        } catch (\Exception $e) {
            report($e);
            $status = false;
            $response = [
                'status' => $status,
                'message' => $e->getMessage()
            ];
            return $response;
        }
    }
}
