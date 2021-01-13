<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHistory extends Model
{
    use HasFactory;

    protected $table='work_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'country',
        'state',
        'city',
        'start_date',
        'end_date',
        'work_gap_days',
        'work_gap_reason'
    ];

    /**
     * Relation with country
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country', 'id');
    }

    /**
     * Relation with state
     */
    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state', 'id');
    }

    /**
     * Relation with city
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city', 'id');
    }

    /**
     * Relation with city
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
