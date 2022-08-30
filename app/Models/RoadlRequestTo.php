<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadlRequestTo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_request_id',
        'clinician_id',
    ];
    
     /**
     * Get Meeting Reasons
     */
    public function patientRequest()
    {
        return $this->hasOne(PatientRequest::class, 'id', 'patient_request_id');
    }
    
}
