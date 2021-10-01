<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'gender','dob', 'phone', 'phone_verified_at', 'type', 'email', 'email_verified_at', 'password', 'status', 'remember_token', 'level', 'api_token', 'designation_id','service_id','latitude','longitude','is_available','avatar',
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
    protected $appends = ['gender_name','avatar_image','phone_format'];

//    protected $dates = [ 'created_at', 'updated_at'];

    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getDobAttribute($value)
    {
        return Carbon::parse(strtotime($value))->format(config('app.date_format'));
    }
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getPhoneFormatAttribute()
    {
        $value=$this->phone;
        if ($value){
            try {
                $cleaned = preg_replace('/[^[:digit:]]/', '', $value);
                preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
                return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
            }catch (\Exception $exception){

            }
        }
    }
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
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getGenderNameAttribute()
    {
        return $this->gender==='1'?'Male':($this->gender==='2'?'Female':'Other');
    }
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getAvatarImageAttribute()
    {
        if (isset($this->image) && !empty($this->image)) {
            return env('WEB_URL').'assets/img/user/'. $this->image;
        } else {
            return env('WEB_URL').'assets/img/user/avatar.jpg';
        }
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:m/d/Y H:i:s',
        'updated_at' => 'datetime:m/d/Y H:i:s',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];
    /**
     *
     */
    static function login($request)
    {
        $status = 0;
        try {
            $username = $request['username'];
            // Check the User from database
            $user = User::select('first_name', 'last_name', 'id', 'type', 'password')
                ->Where(function ($query) use ($username) {
                    $query->where('email', $username);
                    $query->orWhere('phone', $username);
                })->first();
            return $user;
        } catch (\Exception $e) {
            report($e);
            echo $e->getMessage();
            $response = [
                'status' => $status,
                'message' => $e->getMessage()
            ];
            return $response;
        }
    }
    /**
     * Insert the User data from the Employee / Patient
     *
     */
    public static function insert($request)
    {
        try {
            $data = User::create($request);
            return $data->id;
        } catch (\Exception $e) {
            report($e);
            echo $e->getMessage();
            exit;
            return false;
        }
    }
    /**
     * Insert the User data from the Employee / Patient
     *
     */
    public static function gethUserUsingEmail($email)
    {
        try {
            $data = User::select('id', 'first_name', 'last_name', 'password')
                ->where('email', $email)
                ->first();
            return $data;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public static function getUserDetails($userId)
    {
        $user = User::select('first_name', 'last_name', 'id')
                ->Where(function ($query) use ($userId) {
                    $query->where('id', $userId);
                })->first();
        return $user;
    }

    public function designation()
    {
        return $this->hasOne(Designation::class,'id','designation_id');
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class,'user_id','id');
    }
    
    public function myRoom(){
        return $this->hasOne(VirtualRoom::class,'user_id','id');
    }

    public function detail(){
        return $this->hasOne(PatientReferral::class,'user_id','id')->with('service','referral');
    }

    public function leave(){
        return $this->hasOne(EmployeeLeaveManagement::class,'user_id','id');
    }

    public function ccm(){
        return $this->hasMany(CCMReading::class,'user_id','id');
    }

    public function appointment(){
        return $this->hasMany(Appointment::class,'provider1','id');
    }

    public function appointmentOrLeave(){
        return $this->hasManyThrough(
            Appointment::class,
            EmployeeLeaveManagement::class,
            'user_id',
            'provider1',
            'id',
            'id');
    }

    /**
     * applicant
     */
    public function applicant()
    {
        return $this->hasOne(Applicant::class, 'user_id', 'id');
    }

    /**
     * education
     */
    public function education()
    {
        return $this->hasOne(Education::class, 'user_id', 'id');
    }

    /**
     * professional
     */
    public function professional()
    {
        return $this->hasOne(Certificate::class, 'user_id', 'id');
    }

    /**
     * attestation
     */
    public function attestation()
    {
        return $this->hasMany(Attestation::class, 'user_id', 'id');
    }

    /**
     * background
     */
    public function background()
    {
        return $this->hasMany(WorkHistory::class, 'user_id', 'id');
    }

    /**
     * deposit
     */
    public function deposit()
    {
        return $this->hasOne(BankAccount::class, 'user_id', 'id');
    }

    /**
     * documents
     */
    public function documents()
    {
        return $this->hasMany(UploadDocuments::class, 'user_id', 'id');
    }

    /**
     * documents
     */
    public function insurance()
    {
        return $this->hasMany(PatientInsurance::class, 'patient_id', 'id');
    }

    public function caseManager()
    {
        return $this->hasOne(AssignClinicianToPatient::class, 'patient_id', 'id')->where('type','=','1')->with('clinician');
    }

    public function primaryPhysician()
    {
        return $this->hasOne(AssignClinicianToPatient::class, 'patient_id', 'id')->where('type','=','2')->with('clinician');
    }

    public function specialistPhysician()
    {
        return $this->hasOne(AssignClinicianToPatient::class, 'patient_id', 'id')->where('type','=','3')->with('clinician');
    }

    public function caregiverHistory()
    {
        return $this->hasMany(Caregivers::class, 'patient_id', 'id');
    }

    public function caregivers()
    {
        return $this->hasOne(Caregivers::class, 'patient_id', 'id')->orderBy('id','desc');
    }

     public function patientDetail(){
        return $this->hasOne(PatientReferral::class,'user_id','id')->with(['service','filetype']);
    }
    public function caregiverInfo()
    {
        return $this->hasOne(CaregiverInfo::class,'user_id','id');
    }
    public function demographic()
    {
        return $this->hasOne(Demographic::class,'user_id','id');
    }
}
