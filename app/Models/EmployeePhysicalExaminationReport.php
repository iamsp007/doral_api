<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePhysicalExaminationReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'report_details'
    ];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'report_details' => 'array'
    ];

    /**
     * Relation with patient
     */
    public function patient()
    {
        return $this->belongsTo(PatientReferral::class, 'patient_id', 'id');
    }
}
