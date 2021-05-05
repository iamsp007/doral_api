<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarCategory extends Model
{
    use HasFactory;
    protected $table= 'calendar_category';
    protected $fillable = [
        'name',
        'parent_id',
        'service_id',
        'status',
    ];
    
}
