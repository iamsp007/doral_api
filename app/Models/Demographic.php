<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demographic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doral_id',
        'user_id',
        'service_id',
        'patient_id',
        'ssn',
        'medicaid_number',
        'medicare_number',
        'accepted_services',
        'address',
        'language',
        'ethnicity',
        'country_of_birth',
        'employee_type',
        'marital_status',
        'status',
        'notification_preferences',
        'type',
    ];


    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'accepted_services' => 'array',
        'address' => 'array',
        'language' => 'array',
        'notification_preferences' => 'array',
    ];


    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function setSsnAttribute($ssn)
    {
        $ssn = '';
        if ($ssn){
            return str_replace("-","",$ssn);
        }
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
