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
     * The attributes that are append.
     *
     * @var array
     */
    protected $appends = [
        'recipient_signature',
        'interpreter_signature',
        'vaccination_signature',
        'pdf'
    ];

    /**
     * Relation with clinician
     */
    public function clinician()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the recipient signature images.
     *
     * @return string
     */
    public function getRecipientSignatureAttribute()
    {
        if (isset($this['recipient_sign'])) {
            return env('APP_URL').'/storage/covid_form/'.$this['id'].'/'. $this['recipient_sign'];
        } else {
            return null;
        }
    }

    /**
     * Get the interpreter signature images.
     *
     * @return string
     */
    public function getInterpreterSignatureAttribute()
    {
        if (isset($this['interpreter_sign'])) {
            return env('APP_URL').'/storage/covid_form/'.$this['id'].'/'. $this['interpreter_sign'];
        } else {
            return null;
        }
    }

    /**
     * Get the vaccination signature images.
     *
     * @return string
     */
    public function getVaccinationSignatureAttribute()
    {
        if (isset($this['vaccination_sign'])) {
            return env('APP_URL').'/storage/covid_form/'.$this['id'].'/'. $this['vaccination_sign'];
        } else {
            return null;
        }
    }

    /**
     * Get the PDF.
     *
     * @return string
     */
    public function getPdfAttribute()
    {
        if (isset($this['pdf_file'])) {
            return env('APP_URL').'/storage/covid_form/'.$this['id'].'/'. $this['pdf_file'];
        } else {
            return null;
        }
    }
}
