<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModuleAssign extends Model
{
    use HasFactory;
     use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    protected $table='rl_role_module_assign';
    protected $fillable = [
        'role_id','rl_role_module_assign'
    ];
}