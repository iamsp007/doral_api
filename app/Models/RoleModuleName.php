<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModuleName extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='rl_module_name';
    protected $fillable = [
        'name'
    ];

    public function modulePermission(){
        return $this->hasMany(RolePermission::class,'rl_module_name_id');
    }
}