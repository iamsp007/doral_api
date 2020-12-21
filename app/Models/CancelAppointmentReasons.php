<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelAppointmentReasons extends Model
{
    use HasFactory;

    protected $table='cancel_appointment_reasons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason'
    ];

    /**
     * Relation with Appointment
     */
    public function appointment()
    {
        return $this->hasMany('App\Models\Appointment', 'reason_id', 'id');
    }
}
