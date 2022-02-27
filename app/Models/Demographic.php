<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        'gender_at_birth',
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

    protected $appends = ['address_latlng'];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'accepted_services' => 'array',
        'address' => 'array',
        // 'language' => 'array',
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

    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
    
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getAddressLatlngAttribute()
    {
        $address='';
        if ($this->address['address1']){
            $address.= $this->address['address1'];
        }
        if ($this->address['city']){
            $address.= ', '.$this->address['city'];
        }
        if ($this->address['state']){
            $address.= ', '.$this->address['state'];
        }
        if ($this->address['zip_code']){
            $address.= ', '.$this->address['zip_code'];
        }
        
        if ($address){
            $helper = new Helper();
            $response = $helper->getLatLngFromAddress($address);
            if ($response->status==="OK"){
                return $response->results[0]->geometry->location;
            }
        }
    }
}
