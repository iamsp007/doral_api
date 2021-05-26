<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supporter_id',
        'user_id',
        'chat',
        'type',
        'parentID',
    ];

    public function clinician()
    {
        return $this->hasOne(User::class,'id','user_id')->select('id','first_name','last_name');
    }

    public function patient()
    {
        return $this->hasOne(User::class,'id','user_id')->select('id','first_name','last_name');
    }
    protected $dates = [ 'deleted_at' ];
}
