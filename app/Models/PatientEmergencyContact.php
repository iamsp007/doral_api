<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientEmergencyContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'lives_with_patient',
        'have_keys',
        'phone1',
        'phone2',
        'address',
        
    ];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'address_old' => 'array',
    ];
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function setPhone1Attribute($value)
    {
        if ($value){
            $this->attributes['phone1'] = preg_replace("/[^0-9]+/", "", $value);
        }
    }

    public function setPhone2Attribute($value)
    {
        if ($value){
            $this->attributes['phone2'] = preg_replace("/[^0-9]+/", "", $value);
        }
    }
}
