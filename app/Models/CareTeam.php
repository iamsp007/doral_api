<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CareTeam extends Model
{
    use HasFactory, LogsActivity;

     /**
     * The attributes that are casted.
     *
     * @var array
     */
    protected $casts = [
        'detail' => 'array',
    ];
}
