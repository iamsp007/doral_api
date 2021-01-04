<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeRange extends Model
{
    use HasFactory;

    protected $table='age_ranges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'certificate_id',
        'age_range_treated'
    ];

    /**
     * Relation with user
     */
    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id', 'id');
    }
}
