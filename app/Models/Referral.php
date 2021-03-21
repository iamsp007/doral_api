<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;



    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getIconAttribute($value)
    {
        $icon=env('WEB_URL').'assets/icon/Clinician Request.png';
        if ($value) {
            $icon=env('WEB_URL').'assets/icon/'. $value;
        }
        return $icon;
    }
    /**
     * Get the user's Date Of Birth.
     *
     * @return string
     */
    public function getColorAttribute($value)
    {
        $color='green';
        if ($value) {
            $color=$value;
        }
        return $color;
    }
}
