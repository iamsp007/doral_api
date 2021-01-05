<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardCertificate extends Model
{
    use HasFactory;

    protected $table='board_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'certificate_id',
        'certifying_board',
        'status'
    ];

    /**
     * Relation with user
     */
    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id', 'id');
    }
}
