<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

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
    static function login($request)
    {
        $status = 0;
        try {
            $username = $request['username'];
            // Check the User from database
            $user = User::select('first_name', 'last_name', 'id', 'type', 'password')
                ->Where(function ($query) use ($username) {
                    $query->where('email', $username);
                    $query->orWhere('phone', $username);
                })->first();
            return $user;
        } catch (\Exception $e) {
            report($e);
            echo $e->getMessage();
            $response = [
                'status' => $status,
                'message' => $e->getMessage()
            ];
            return $response;
        }

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
            echo $e->getMessage();exit;
            return false;
        }
    }
}
