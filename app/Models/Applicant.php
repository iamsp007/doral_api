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
        'family_detail',
        'military_detail',
        'security_detail',
        'address_detail',
        'prior_detail',
        'reference_detail',
        'employer_detail',
        'education_detail',
        'language_detail',
        'skill_detail',
        'emergency_detail',
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
        'signature',
        'workHistory_detail',
        'professional_detail',
    ];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'family_detail' => 'array',
        'military_detail' => 'array',
        'security_detail' => 'array',
        'address_detail' => 'array',
        'prior_detail' => 'array',
        'reference_detail' => 'array',
        'employer_detail' => 'array',
        'education_detail' => 'array',
        'language_detail' => 'array',
        'skill_detail' => 'array',
        'emergency_detail' => 'array',
        'payroll_details' => 'array',
        'workHistory_detail' => 'array',
        'professional_detail' => 'array',
    ];

    protected $appends = ['signature_url','is_signature_added'];

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

    /**
     * documents
     */
    public function documents()
    {
        return $this->hasMany(UploadDocuments::class, 'user_id', 'user_id');
    }

    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getSignatureUrlAttribute()
    {
        if (isset($this->signature) && !empty($this->signature)) {
            return url('storage/signature/'.$this->user_id.'/'.$this->signature);
        }
        
        return null;
        
    }

    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getIsSignatureAddedAttribute()
    {
        if (isset($this->signature) && !empty($this->signature)) {
            return true;
        }
        
        return false;
    }
}
