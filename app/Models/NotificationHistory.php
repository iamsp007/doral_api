<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'sender_id',
        'receiver_id',
        'request_id',
        'model_type',
        'status',
        'is_read',
    ];
    public function sender(){

        return $this->belongsTo(User::class,'sender_id','id')->select('id','first_name','last_name');
    }

    public function receiver(){

        return $this->belongsTo(User::class,'receiver_id','id')->select('id','first_name','last_name');
    }

    public function request(){

        return $this->belongsTo(PatientRequest::class,'request_id','id')->select('id','test_name','reason');
    }
}
