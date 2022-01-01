<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareTeam extends Model
{
    use HasFactory;

     /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'detail' => 'array',
    ];
}