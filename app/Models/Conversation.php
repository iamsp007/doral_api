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
        'sender_id',
        'receiver_id',
        'chat',
        'type',
        'parentID',
    ];

    protected $dates = [ 'deleted_at' ];

    public function user()
    {
        return $this->hasOne(User::class,'id','sender_id');
    }
}
