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
    public function getIconAttribute()
    {
        if (isset($this->icon) && !empty($this->icon)) {
            return env('WEB_URL').'assets/icon/'. $this->icon;
        } else {
            return env('WEB_URL').'assets/icon/Clinician Request.png';
        }
    }
}
