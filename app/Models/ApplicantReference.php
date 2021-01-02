<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantReference extends Model
{
    use HasFactory;

    protected $table='applicant_references';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'applicant_id',
        'referance_name',
        'reference_address',
        'reference_phone',
        'reference_relationship',
    ];

    /**
     * Relation with applicant
     */
    public function applicant()
    {
        return $this->belongsTo('App\Models\Applicant', 'applicant_id', 'id');
    }
}
