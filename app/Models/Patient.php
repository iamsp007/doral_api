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
        'first_name', 'last_name', 'gender', 'address1', 'address2', 'zip', 'phone', 'email', 'dob', 'ssn', 'npi', 'role_id', 'designation_id', 'emg_first_name', 'emg_last_name', 'emg_address1', 'emg_address2', 'emg_zip', 'emg_phone', 'emg_email', 'join_date', 'Employeement _type', 'status', 'user_id', 'medicaid_number', 'medicare_number', 'cin_no', 'service_key'
    ];

    /**
     * Get the comments for the blog post.
     */
    public function patientService()
    {
        return $this->hasMany('App\Models\patientService');
    }

    /**
     * Get the PatientInsurance details
     */
    public function patientInsurance()
    {
        return $this->hasMany('App\Models\patientInsurance');
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
    public static function updatePatient($id, $request)
    {
        try {
            $data = patient::where('id', $id)->update($request);
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
            $response = $patient->patientService()->createMany($request['PatientInsurance']);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }
}
