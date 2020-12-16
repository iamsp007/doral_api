<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadlInformation extends Model
{
    use HasFactory;

    protected $table='roadl_information';
    protected $primaryKey='id';

    public function user(){

        return $this->hasOne(User::class,'id','user_id');
    }
}
