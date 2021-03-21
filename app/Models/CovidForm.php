<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CovidForm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'dose',
        'patient_name',
        'phone',
        'data',
        'recipient_sign',
        'interpreter_sign',
        'vaccination_sign',
        'status',
    ];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Relation with patient
     */
    public function patient()
    {
        return $this->belongsTo(PatientReferral::class, 'user_id', 'id');
    }
}
