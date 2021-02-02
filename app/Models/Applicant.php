<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $table='applicants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'applicant_name',
        'other_name',
        'ssn',
        'phone',
        'home_phone',
        'date',
        'us_citizen',
        'immigration_id',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip',
        'address_life',
        'bonded',
        'refused_bond',
        'convicted_crime',
        'emergency_name',
        'emergency_address',
        'emergency_phone',
        'emergency_relationship',
    ];

    /**
     * Relation with referances
     */
    public function references()
    {
        return $this->hasMany('App\Models\ApplicantReference', 'applicant_id', 'id');
    }

    /**
     * Relation with state
     */
    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state', 'id');
    }

    /**
     * Relation with city
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city', 'id');
    }

    /**
     * Relation with city
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
