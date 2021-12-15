<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_device_id',
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
