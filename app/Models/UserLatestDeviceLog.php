<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLatestDeviceLog extends Model
{
    use HasFactory;

    protected $table = 'user_latest_device_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_device_id',
        'patient_id',
        'value',
        'reading_time',
        'level',
        'status',
    ];

    /**
     * Relation with user
     */
    public function userDevice()
    {
        return $this->belongsTo('App\Models\UserDevice', 'user_device_id', 'id');
    }
}
