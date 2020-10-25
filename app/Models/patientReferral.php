<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class patientReferral extends Model
{
    use HasFactory;
    protected $fillable = array('referral_id', 'first_name', 'last_name', 'dob');
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
            report($e);
            echo $e->getMessage();
            return false;
        }
    }
}
