<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateLicense extends Model
{
    use HasFactory;

    protected $table='state_licenses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'certificate_id',
        'license_state',
        'license_number'
    ];

    /**
     * Relation with user
     */
    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id', 'id');
    }

    /**
     * Relation with state
     */
    public function licenseState()
    {
        return $this->belongsTo('App\Models\State', 'license_state', 'id');
    }
}
