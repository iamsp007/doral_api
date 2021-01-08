<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first_name', 'last_name', 'gender', 'address1', 'address2', 'zip', 'phone', 'email', 'dob', 'ssn', 'npi', 'role_id', 'designation_id', 'emg_first_name', 'emg_last_name', 'emg_address1', 'emg_address2', 'emg_zip', 'emg_phone', 'emg_email', 'join_date', 'Employeement _type', 'status', 'user_id', 'medicaid_number', 'medicare_number', 'cin_no', 'service_key'
    ];

    /**
     * Get the comments for the blog post.
     */
    public function patientService()
    {
        return $this->hasMany('App\Models\PatientService');
    }

    /**
     * Get the PatientInsurance details
     */
    public function patientInsurance()
    {
        return $this->hasMany('App\Models\PatientInsurance');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * 
     */
    public static function searchByEmailNamePhone($keyword)
    {
        try {
            $resp = Patient::select(\DB::raw('concat(first_name, " ",last_name) as name'), 'email', 'phone')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere(\DB::raw('concat(first_name, " ",last_name)'), 'like', '%' . $keyword . '%')
                ->get()
                ->toArray();
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
            $data = patient::create($request);
            return $data;
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }
    /**
     * Update patient information based on id
     */
    public static function updatePatient($request)
    {
        try {
            $data = Patient::firstOrCreate($request);
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }

    /**
     * Update patient services information based on id
     */
    public static function updateServices($id, $request)
    {
        try {
            $patient = Patient::find($id);
            $response = $patient->patientService()->createMany($request['PatientServices']);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }

    /**
     * Update patient services information based on id
     */
    public static function updateInsurance($id, $request)
    {
        try {
            $patient = Patient::find($id);
            $response = $patient->patientInsurance()->createMany($request['PatientInsurance']);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }

    public static function getPatientUsingSsnAndDob($input)
    {
        try {
            $data = Patient::with('user')->where('ssn', 'LIKE', '%'.$input['ssn'])
                ->where('dob', $input['dob'])
                ->first();
            return $data;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
