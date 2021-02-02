<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualRoom extends Model
{
    use HasFactory;

    protected $table='virtual_room';
    protected $primaryKey='id';

    protected $appends = ['meeting_detail'];

    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getMeetingDetailAttribute()
    {
        $value=$this->zoom_response;
        if ($value){
            $value = json_decode($value);
            return $value;
        }
        return null;
    }
}
