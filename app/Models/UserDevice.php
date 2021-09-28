<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'device_type',
        'status',
        'patient_id'
    ];

    /**
     * Relation with user
     */
    public function demographic()
    {
        return $this->belongsTo('App\Models\Demographic', 'patient_id', 'user_id');
    }

    /**
     * Relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'patient_id', 'id');
    }
}
