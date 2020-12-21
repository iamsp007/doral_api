<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualRoom extends Model
{
    use HasFactory;

    protected $table='virtual_room';
    protected $primaryKey='id';
}
