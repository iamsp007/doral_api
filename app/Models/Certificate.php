<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $table='certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'medicare_enrolled',
        'medicare_state',
        'medicare_number',
        'medicaid_enrolled',
        'medicaid_state',
        'medicaid_number',
        'federal_dea_id'
    ];

    /**
     * Relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * Relation with age ranges
     */
    public function ageRanges()
    {
        return $this->hasMany('App\Models\AgeRange', 'certificate_id', 'id');
    }

    /**
     * Relation with state licenses
     */
    public function stateLicenses()
    {
        return $this->hasMany('App\Models\StateLicense', 'certificate_id', 'id');
    }

    /**
     * Relation with board certificates
     */
    public function boardCertificates()
    {
        return $this->hasMany('App\Models\BoardCertificate', 'certificate_id', 'id');
    }
}
