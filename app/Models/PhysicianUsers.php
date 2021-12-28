<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianUsers extends Model
{
    use HasFactory;

    public $connection = 'mysql2';

    protected $table = "physician_users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'speciality_id',
        'first_name',
        'last_name',
        'gender',
        'ssn_no',
        'dea_no',
        'add1',
        'add2',
        'street1',
        'city',
        'state',
        'zip_code',
        'citizenship_status',
        'expire_month',
        'expire_year',
        'doc_num',
        'doc_exp',
        'date_of_birth',
        'ssn_last_four',
        'profession',
        'license_number',       
        'npi_no',
        'email',        
    ];
}