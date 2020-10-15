<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'dob', 'phone', 'type', 'email', 'email_verified_at', 'password', 'status', 'remember_token', 'level', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * 
     */
    function login($request)
    {
    }
    /**
     * 
     */
    public function generateToken()
    {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }
    /**
     * Insert the User data from the Employee / Patient 
     * 
     */
    public static function insert($request)
    {
        try {
            $data = User::create($request);
            return $data->id;
        } catch (\Exception $e) {
            report($e);
            echo $e->getMessage();
            return false;
            
        }
    }
}
