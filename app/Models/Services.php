<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status'
    ];
    /**
     * Insert data into services table
     */
    public static function insert($request)
    {
        $status = 0;
        try {
            $data = Services::create($request);
            return $data;
        } catch (\Exception $e) {
            return false;
            exit;
        }
    }
    /**
     * Insert data into services table
     */
    public static function getAllServices($request)
    {
        $status = 0;
        try {
            $data = Services::select('id', 'name')
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
    public static function setServices($id, $request)
    {
        try {
            $data = Services::where('id', $id)->update($request);
            $data = Services::find($id);
            return $data;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
            exit;
        }
    }    
}
