<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table='education';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'medical_institute_name',
        'medical_institute_address',
        'medical_institute_city',
        'medical_institute_state',
        'medical_institute_year_started',
        'medical_institute_year_completed',
        'residency_institute_name',
        'residency_institute_address',
        'residency_institute_city',
        'residency_institute_state',
        'residency_institute_year_started',
        'residency_institute_year_completed',
        'fellowship_institute_name',
        'fellowship_institute_address',
        'fellowship_institute_city',
        'fellowship_institute_state',
        'fellowship_institute_year_started',
        'fellowship_institute_year_completed'
    ];

    /**
     * Relation with city
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * Relation with state
     */
    public function medicalInstituteState()
    {
        return $this->belongsTo('App\Models\State', 'medical_institute_state', 'id');
    }

    /**
     * Relation with city
     */
    public function medicalInstituteCity()
    {
        return $this->belongsTo('App\Models\City', 'medical_institute_city', 'id');
    }

    /**
     * Relation with state
     */
    public function residencyInstituteState()
    {
        return $this->belongsTo('App\Models\State', 'residency_institute_state', 'id');
    }

    /**
     * Relation with city
     */
    public function residencyInstituteCity()
    {
        return $this->belongsTo('App\Models\City', 'residency_institute_city', 'id');
    }

    /**
     * Relation with state
     */
    public function fellowshipInstituteState()
    {
        return $this->belongsTo('App\Models\State', 'fellowship_institute_state', 'id');
    }

    /**
     * Relation with city
     */
    public function fellowshipInstituteCity()
    {
        return $this->belongsTo('App\Models\City', 'fellowship_institute_city', 'id');
    }
}
