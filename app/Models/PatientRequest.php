<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class PatientRequest extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'clincial_id',
        'test_name',
        'type_id',
        'parent_id',
        'latitude',
        'longitude',
        'reason',
        'status',
        'preparation_time',
        'preparasion_date',
        'accepted_time',
        'arrived_time',
        'complated_time',
        'distance',
        'travel_time',
    ];

    public function detail(){

        return $this->belongsTo(User::class,'clincial_id','id')->select('id','latitude','longitude','first_name','last_name','email');
    }

    public function patient(){

        return $this->belongsTo(User::class,'user_id','id')->select('id','latitude','longitude','first_name','last_name','email');
    }
    public function patientDetail(){

        return $this->hasOne(User::class,'id','user_id')->with('detail');
    }
    public function ccrm(){
        return $this->hasMany(CCMReading::class,'user_id','user_id');
    }
    public function routes(){
        return $this->hasMany(RoadlInformation::class,'patient_requests_id','id')->with('user');
    }
    /**
     * Get Meeting Reasons
     */
    public function meeting()
    {
        return $this->hasOne(VirtualRoom::class, 'appointment_id', 'id');
    }
    /**
     * Get Meeting Reasons
     */
    public function requests()
    {
        return $this->hasMany(PatientRequest::class, 'parent_id', 'parent_id')->orderBy('id','desc')->with(['requestType','detail']);
    }
    /**
     * Get Meeting Reasons
     */
    public function requestType()
    {
        return $this->hasOne(Referral::class, 'role_id', 'type_id')->select('id','role_id','name','color','icon');
    }

    public function getSymptomsAttribute($value){
        if ($value){
            $symtoms = SymptomsMaster::whereIn('id',explode(',',$value))->pluck('name');
            if ($symtoms){
                return implode(',',$symtoms->toArray());
            }
            return '-';
        }
        return '-';
    }

    public function getDiesesAttribute($value){
        if ($value){
            $data = DiesesMaster::whereIn('id',explode(',',$value))->pluck('name');
            if ($data){
                return implode(',',$data->toArray());
            }
            return '-';
        }
        return '-';
    }
}
