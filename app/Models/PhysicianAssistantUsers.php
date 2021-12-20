<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicianAssistantUsers extends Model
{
    use HasFactory;

    public $connection = 'mysql2';

    protected $table = "physician_assistant_users";

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
        'ssn_no',
        'dea_no',
        'zip_code',
        'expire_month',
        'expire_year',
        'date_of_birth',
    ];
}
