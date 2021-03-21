<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Company extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;
    // protected $guard = 'referral';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'address1', 'address2', 'zip', 'phone', 'npi', 'np_id', 'referal_id', 'verification_comment', 'status', 'administrator_phone_no'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
 
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function setPhoneAttribute($value)
    {
        if ($value){
            $this->attributes['phone'] = preg_replace("/[^0-9]+/", "", $value);
        }
    }

    public function setAdministratorPhoneNoAttribute($value)
    {
        if ($value){
            $this->attributes['administrator_phone_no'] = preg_replace("/[^0-9]+/", "", $value);
        }
    }

    public static function getCompanyDetails($company = 0)
    {
        $company = Company::where('id1', '=', $company)->first();
        return $company;
    }

    public function referral(){
        return $this->hasOne(Referral::class,'id','referal_id')
            ->where('guard_name','=','referral');
    }

    public function paymentInfo(){
        return $this->hasMany(CompanyPaymentPlanInfo::class);
    }
}
