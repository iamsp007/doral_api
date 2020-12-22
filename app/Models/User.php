<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

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

////
//    protected $dates = [ 'created_at', 'updated_at'];

    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getDobAttribute($value)
    {
        return Carbon::parse(strtotime($value))->format(config('app.date_format'));
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
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
            echo $e->getMessage();
            exit;
            return false;
        }
    }
    /**
     * Insert the User data from the Employee / Patient
     *
     */
    public static function gethUserUsingEmail($email)
    {
        try {
            $data = User::select('id', 'first_name', 'last_name', 'password')
                ->where('email', $email)
                ->first();
            return $data;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public static function getUserDetails($userId)
    {
        $user = User::select('first_name', 'last_name', 'id')
                ->Where(function ($query) use ($userId) {
                    $query->where('id', $userId);
                })->first();
        return $user;
    }

    public function myRoom(){
        return $this->hasOne(VirtualRoom::class,'id','user_id');
    }

}
