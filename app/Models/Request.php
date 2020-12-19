<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'user_id', 'support_type'
    ];
    /**
     * Insert data into services table
     */
    public static function insert($request)
    {
        $status = 0;
        try {
            $data = Request::create($request);
            return $data;
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }
    /**
     * Insert data into services table
     */
    public static function getAllRequests($request)
    {
        $status = 0;
        try {
            $data = Request::select('id', 'role_id', 'user_id', 'support_type')
                ->get();
            return $data;
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }
    /**
     * Update records into service table
     */
    public static function setRequest($id, $request)
    {
        try {
            $data = Request::where('id', $id)->update($request);
            $data = Request::find($id);
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }    
}
