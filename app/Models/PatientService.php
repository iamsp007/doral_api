<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class patientService extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id', 'service_id', 'company_id', 'employee_id', 'comment', 'request_date', 'status'
    ];

    /**
     * Get Services from master table
     */
    public function service()
    {
        return $this->belongsTo('App\Models\Service');
    }
}
