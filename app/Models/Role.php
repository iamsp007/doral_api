<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    public function moduleAssign(){
        return $this->hasMany(RoleModuleAssign::class,'role_id');
    }

    public function rolePermission(){
        return $this->hasMany(RolePermissionAssign::class,'role_id');
    }
}
