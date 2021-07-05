<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'field'
    ];

    /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'field' => 'array',
    ];
}
