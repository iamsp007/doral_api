<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AssignClinicianToPatient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'clinician_id'
    ];

    /**
     * patient
     */
    public function patient()
    {
        return $this->hasOne(User::class, 'id', 'patient_id');
    }

    /**
     * clinician
     */
    public function clinician()
    {
        return $this->hasOne(User::class, 'id', 'clinician_id');
    }
}
